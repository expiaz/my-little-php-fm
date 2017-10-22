<?php

namespace App\Core\Http\Router;

use App\Core\Utils\ParameterBag;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Uri;
use InvalidArgumentException;
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

    /**
     * @param string $method
     * @param string $path
     * @param $handler
     * @param string $name
     * @return RouteBag
     */
    private function add(string $method, string $path, string $handler, string $name): RouteBag
    {
        $routes = array_map(
            function(string $routePath) use($method, $name, $handler): Route
            {
                $r = new Route($name, $routePath, $handler);
                $this->routes[$method][] = $r;
                return $r;
            },
            $this->preprocessOptionnals($path)
        );

        usort($this->routes[$method], function (Route $a, Route $b) {
            return $b->getRegex() <=> $a->getRegex();
        });

        return new RouteBag($routes);
    }

    /**
     * add a route for GET requests
     * @param string $path
     * @param $handler
     * @param null|string $name
     * @return RouteBag
     */
    public function get(string $path, $handler, string $name): RouteBag
    {
        return $this->add(Request::GET, $path, $handler, $name);
    }

    /**
     * add a route for POST requests
     * @param string $path
     * @param $handler
     * @param null|string $name
     * @return RouteBag
     */
    public function post(string $path, $handler, string $name): RouteBag
    {
        return $this->add(Request::POST, $path, $handler, $name);
    }

    /**
     * add a route for PUT requests
     * @param string $path
     * @param $handler
     * @param null|string $name
     * @return RouteBag
     */
    public function put(string $path, $handler, string $name): RouteBag
    {
        return $this->add(Request::PUT, $path, $handler, $name);
    }

    /**
     * add a route for DELETE requests
     * @param string $path
     * @param $handler
     * @param null|string $name
     * @return RouteBag
     */
    public function delete(string $path, $handler, string $name): RouteBag
    {
        return $this->add(Request::DELETE, $path, $handler, $name);
    }

    /**
     * try to apply to regex of each route for the request uri path
     * @param Request $request
     * @return Match|null if a route is found or null otherwise
     */
    public function match(Request $request): ?Match
    {
        if(! array_key_exists($request->getMethod(), $this->routes)){
            throw new InvalidArgumentException("[Router::match] method {$request->getMethod()} is not allowed");
        }

        foreach ($this->routes[$request->getMethod()] as $route){

            /**
             * @var $route Route
             */
            if(preg_match(
                $route->getRegex(),
                $request->getUri()->getPath(),
                $parameters
            )){
                $params = new ParameterBag();
                $arguments = $route->getParameters();
                $nb = 0;
                foreach ($arguments as $argument => $filter){
                    $params->set($argument, $parameters[++$nb]);
                }
                return new Match($route, $params);
            }

        }

        return null;
    }

    /**
     * @param string $name
     * @param array|null $parameters
     * @return Uri|null is no route found
     */
    public function build(string $name, ?array $parameters = []): ?Uri
    {
        foreach ($this->routes as $routes){
            /**
             * @var $route Route
             */
            foreach ($routes as $route) {
                if($name === $route->getName()) {
                    // apply arguments to the route
                    // e.g '/test/{name: regex}' with '[name => 4]' becomes '/test/4'
                    $rebuiltPath = $route->buildUri($parameters);
                    if ($rebuiltPath !== null) {
                        return $rebuiltPath;
                    }
                }
            }
        }

        return null;
    }

}