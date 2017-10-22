<?php

namespace Tests\Core;

use App\Core\App;
use App\Core\Container;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Router\Router;
use App\Core\Renderer;
use PHPUnit\Framework\TestCase;
use Tests\Module\Test\Controller\TestController;

class AppTest extends TestCase
{
    /**
     * @var Container
     */
    private $container;

    protected function setUp()
    {
        $renderer = new Renderer();
        $router = new Router();
        $renderer->addGlobal('renderer', $renderer);
        $renderer->addGlobal('router', $router);

        $this->container = new Container();
        $this->container->set(Router::class, $router);
        $this->container->set(Renderer::class, $renderer);
    }

    public function testConstruct()
    {
        $modules = [
            TestController::class
        ];

        new App($this->container, $modules);

        self::assertEquals('testValue', $this->container->get('testKey'));
    }

    public function testRun()
    {

        $modules = [
            TestController::class
        ];

        $app = new App($this->container, $modules);

        $request = new Request(Request::GET, '/test/jean');

        $response = $app->run($request);

        self::assertInstanceOf(Response::class, $response);
        self::assertEquals(200, $response->getStatusCode());
        self::assertContains('jean', $response->getBody());
    }

    public function testRunNotFound()
    {
        $modules = [

        ];

        $app = new App($this->container, $modules);

        $request = new Request(Request::GET, '/test/jean');

        $response = $app->run($request);

        self::assertInstanceOf(Response::class, $response);
        self::assertEquals(404, $response->getStatusCode());
        self::assertEquals('<h1>Error 404</h1>', $response->getBody());
    }

}