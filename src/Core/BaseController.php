<?php

namespace App\Core;

use App\Core\Http\Router\Router;

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

    // to enable the module
    public abstract function __invoke(Container $container, Router $router, Renderer $renderer);
}