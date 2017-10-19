<?php

namespace Tests\Src\Controller;

use App\Core\BaseController;
use App\Core\Container;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Router\Router;
use App\Core\Renderer;

class TestController extends BaseController
{

    public function indexAction(Request $request): Response
    {

    }

    public function testAction(Request $request): Response
    {

    }

    /**
     * @param Container $container
     * @param Router $router
     * @param Renderer $renderer
     */
    public function __invoke(Container $container, Router $router, Renderer $renderer)
    {
        // TODO: Implement __invoke() method.
    }
}