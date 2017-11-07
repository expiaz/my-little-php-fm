<?php

namespace App\Core;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Router\Middleware;
use App\Core\Http\Router\Route;
use App\Core\Utils\Resolver;

class Dispatcher
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * resolve then dispatch the given request to the handler, send back the response from the handler
     * @param Route $route
     * @param Request $request
     * @return Response
     */
    public function dispatch(Route $route, Request $request): Response
    {
        /**
         * @var $resolver Resolver
         */
        $resolver = $this->container->get(Resolver::class);
        $middlewareHandlers = $route->getMiddlewares();
        // route middleware to call it's handler
        $head = new Middleware($resolver, $route->getHandler(), null);
        // let's build a linked list from the bottom to the top
        while(! $middlewareHandlers->empty()){
            $head = new Middleware($resolver, $middlewareHandlers->pop(), $head);
        }

        return $head->call($request);
    }
}