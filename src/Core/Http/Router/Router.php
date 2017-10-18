<?php

namespace App\Core\Http\Router;

use App\Core\Http\Request;
use PHPUnit\Runner\Exception;

final class Router
{


    /**
     * @var Route[]
     */
    private $routes;

    /**
     * @var
     */
    private $routesMap;

    public function __construct()
    {
        $this->routes = [
            Request::GET => [],
            Request::POST => [],
            Request::PUT => [],
            Request::DELETE => []
        ];
        $this->routesMap = [];
    }

    /**
     * preprocess a path to determine possibles routes from optionnals
     * e.g '/test[/opt]' becomes '/test' and '/test/opt'
     * @param string $path
     * @return array
     */
    private function preprocessOptionnals(string $path): array
    {
        $routeWithoutClosingOptionals = rtrim($path, ']');
        $numOptionals = strlen($path) - strlen($routeWithoutClosingOptionals);

        // Split on [ while skipping placeholders
        $segments = preg_split('~' . Route::VARIABLE_REGEX . '(*SKIP)(*F) | \[~x', $routeWithoutClosingOptionals);
        if ($numOptionals !== count($segments) - 1) {
            // If there are any ] in the middle of the route, throw a more specific error message
            if (preg_match('~' . Route::VARIABLE_REGEX . '(*SKIP)(*F) | \]~x', $routeWithoutClosingOptionals)) {
                throw new Exception("[Router::preprocessOptionnals] Optional segments can only occur at the end of a route");
            }
            throw new Exception("[Router::preprocessOptionnals] Number of opening '[' and closing ']' does not match");
        }

        /*
         * for '/test[/opt]
         * segment = [
         *      '/test'
         *      '/opt'
         * ]
         */
        $currentRoute = '';
        $routesPossibles = [];
        foreach ($segments as $n => $segment) {
            if ($segment === '' && $n !== 0) {
                throw new Exception("[Router::preprocessOptionnals] Empty optional part");
            }
            $currentRoute .= $segment;
            $routesPossibles[] = $currentRoute;
        }

        // ['/test', '/test/opt']
        return $routesPossibles;
    }

    private function add(string $method, string $path, $handler, string $name): Route
    {
        foreach ($this->preprocessOptionnals($path) as $routePath){
            $route = new Route($name, $routePath, $handler);
            $this->routes[$method] = $route;
            $this->routesMap[$route->getName()] = $route;
        }
    }

    /**
     * add a route for GET requests
     * @param string $path
     * @param $handler
     * @param null|string $name
     * @return Route
     */
    public function get(string $path, $handler, string $name): Route
    {
        return $this->add(Request::GET, $path, $handler, $name);
    }

    /**
     * add a route for POST requests
     * @param string $path
     * @param $handler
     * @param null|string $name
     * @return Route
     */
    public function post(string $path, $handler, string $name): Route
    {
        return $this->add(Request::POST, $path, $handler, $name);
    }

    /**
     * add a route for PUT requests
     * @param string $path
     * @param $handler
     * @param null|string $name
     * @return Route
     */
    public function put(string $path, $handler, string $name): Route
    {
        return $this->add(Request::PUT, $path, $handler, $name);
    }

    /**
     * add a route for DELETE requests
     * @param string $path
     * @param $handler
     * @param null|string $name
     * @return Route
     */
    public function delete(string $path, $handler, string $name): Route
    {
        return $this->add(Request::DELETE, $path, $handler, $name);
    }

    /**
     *
     * @param Request $request
     * @return Match if a route is found or null else
     */
    public function match(Request $request): Match
    {
        if(! array_key_exists($request->getMethod(), $this->routes)){
            throw new \InvalidArgumentException("[Router::match] method {$request->getMethod()} is not allowed");
        }

        foreach ($this->routes[$request->getMethod()] as $route){

            $match = new Match($route);
            $arguments = $route->getParameters();

            if(preg_match(
                $route->getRegex(),
                $request->getUri()->getPath(),
                $parameters
            )){
                $nb = 0;
                foreach ($arguments as $argument){
                    $match->addParameter($argument, $parameters[++$nb]);
                }

                return $match;
            }
        }

        return null;
    }

}