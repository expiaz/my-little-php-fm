<?php

namespace Tests\Core\Http\Router;

use App\Controller\TestController;
use App\Core\Http\Request;
use App\Core\Http\Router\Match;
use App\Core\Http\Router\Router;
use App\Core\Http\Uri;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{

    public function testMatch(){

        $router = new Router();

        $router->get('/test/{id: \d+}', TestController::class . '::router', 'test.router');

        $request = new Request(Request::GET, '/test/4');

        $match = $router->match($request);

        self::assertInstanceOf(Match::class, $match);
        self::assertEquals('4', $match['id']);
        self::assertEquals(1, count($match->getParameters()));
        self::assertEquals('test.router', $match->getRoute()->getName());
        self::assertEquals('/test/{id: \d+}', $match->getRoute()->getPath());
    }

    public function testBuild(){

        $router = new Router();

        $router->get('/test/{name: \w+}', TestController::class . '::index', 'test.router');

        $uri = $router->build('test.router', [
            'name' => 'john'
        ]);

        self::assertInstanceOf(Uri::class, $uri);
        self::assertEquals('/test/john', $uri->getPath());
    }


}