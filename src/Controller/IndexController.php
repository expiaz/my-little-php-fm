<?php

namespace App\Controller;

use App\Core\BaseController;
use App\Core\Container;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Router\Router;
use App\Core\Renderer;

class IndexController extends BaseController
{

    public  function __invoke(Container $container, Router $router, Renderer $renderer)
    {
        $renderer->addNamespace('index', dirname(__DIR__) . 'View');

        $router->get('/hello/{id:\d+}', IndexController::class . '::indexAction', 'index');
    }

    public function indexAction(Request $request): Response
    {
        return (new Response())->write($this->renderer->render('@index/index', [
                'id' => $request->getParameters()->get('id')
            ]));
    }


}