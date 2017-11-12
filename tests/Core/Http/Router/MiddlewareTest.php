<?php

namespace Tests\Core\Http\Router;

use App\Core\Bootstraper;
use App\Core\Container;
use App\Core\Dispatcher;
use App\Core\Http\Request;
use App\Core\Http\Router\Route;
use App\Core\Renderer;
use PHPUnit\Framework\TestCase;
use Tests\Module\Test\Controller\TestController;

class MiddlewareTest extends TestCase
{

    public function testCall()
    {
        $route = new Route('test.route', '/abc/de', TestController::class . '::testAction');
        $route->use(TestController::class . '::chainedMiddleware');
        $request = new Request(Request::GET, '/abc/de');
        $response = (
            new Dispatcher((new Bootstraper(TEST_CONFIG_FILE))->bootstrap())
        )->dispatch($route, $request);

        self::assertEquals('value',$request->getParameters()->get('middleware'));
        self::assertContains('<h1>Test</h1>', $response->getBody());
    }

    public function testCallBreak()
    {
        $route = new Route('test.route', '/abc/de', TestController::class . '::testAction');
        $route->use(TestController::class . '::breakMiddleware');
        $request = new Request(Request::GET, '/abc/de');
        $response = (new Dispatcher((
            new Bootstraper(TEST_CONFIG_FILE))->bootstrap()
        ))->dispatch($route, $request);

        self::assertEquals(300, $response->getStatusCode());
        self::assertContains('middleware', $response->getBody());
    }

}