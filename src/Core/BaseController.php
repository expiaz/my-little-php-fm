<?php

namespace App\Core;

use App\Core\Http\Router\Router;
use PDO;

abstract class BaseController
{

    /**
     * @var Container
     */
    protected $container;
    /**
     * @var Renderer
     */
    protected $renderer;
    /**
     * @var Router
     */
    protected $router;


    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->renderer = $container->get(Renderer::class);
        $this->router = $container->get(Router::class);
    }

    /**
     * enable route and views registering, called from a static context (no $this)
     * @param Container $container
     * @param Router $router
     * @param Renderer $renderer
     * @return void
     */
    public static abstract function register(Container $container, Router $router, Renderer $renderer): void;
}