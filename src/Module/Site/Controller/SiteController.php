<?php

namespace App\Module\Site\Controller;

use App\Core\BaseController;
use App\Core\Container;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Router\Router;
use App\Core\Renderer;

class SiteController extends BaseController{

    public const MODULE_PATH = MODULE . 'Site/';

    /**
     * enable route and views registering, called from a static context (no $this)
     * @param Container $container
     * @param Router $router
     * @param Renderer $renderer
     * @return void
     */
    public static function register(Container $container, Router $router, Renderer $renderer): void
    {
        $renderer->addNamespace('site', self::MODULE_PATH . 'View');
        $renderer->addNamespace('layout', self::MODULE_PATH . 'View/layout');

        $router->get('/', SiteController::class . '::homeAction', 'site.home');
        $router->get('/info', SiteController::class . '::infoAction', 'site.info');
    }


    public function homeAction(Request $request): Response
    {
        return new Response(200, [], $this->renderer->render('@site/home', [
            'title' => 'Site SIL3'
        ]));
    }

    public function infoAction(Request $request): Response
    {
        return new Response(200, [], $this->renderer->render('@site/info'));
    }


}