<?php

namespace Tests\Module\Test\Controller;

use App\Core\BaseController;
use App\Core\Container;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Router\Router;
use App\Core\Renderer;

class TestController extends BaseController
{
    public const MODULE_PATH = MODULE_TEST . 'Test/';

    /**
     * enable route and views registering, called from a static context (no $this)
     * @param Container $container
     * @param Router $router
     * @param Renderer $renderer
     * @return void
     */
    public static function register(Container $container, Router $router, Renderer $renderer): void
    {
        $container->set('testKey', 'testValue');
        $renderer->addNamespace('test', self::MODULE_PATH . 'View');
        $router->get('/test/{name: \w+}[/{opt: \d+}]', TestController::class . '::testAction', 'test.route');
    }

    public function testAction(Request $request): Response
    {
        return new Response(200, [], $this->renderer->render('@test/test', [
            'name' => $request->getParameters()->get('name'),
            'opt' => $request->getParameters()->get('opt', 'value')
        ]));
    }

}