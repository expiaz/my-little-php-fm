<?php

namespace App\Core\Http\Router;

/**
 * Used as a proxy, apply attribute modifications to each of the contained route,
 * it is necessary since multiple routes can be created from the same path (with optionnals)
 * Class RouteBag
 * @package App\Core\Http\Router
 */
final class RouteBag
{

    /**
     * @var Route[]
     */
    private $routes;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * adds a middleware to every contained route
     * @param string $middleware
     * @return RouteBag
     */
    public function use(string $middleware): RouteBag
    {
        foreach ($this->routes as $route){
            $route->use($middleware);
        }

        return $this;
    }

    /**
     * rename every contained route
     * @param string $name
     * @return RouteBag
     */
    public function as(string $name): RouteBag
    {
        foreach ($this->routes as $route){
            $route->as($name);
        }

        return $this;
    }

}