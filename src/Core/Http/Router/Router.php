<?php

namespace App\Core\Http\Router;

use App\Core\Http\Request;
use App\Core\Http\Uri;

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

    private function add(string $method, string $path, $handler, string $name): Route
    {
        $route = new Route($name, $path, $handler);
        $this->routes[$method][] = $route;
        $this->routesMap[$route->getName()] = $route;

        // order the routes from littlest to biggest for matching
        usort($this->routes[$method], function (Route $a, Route $b) {
            return $b->getNormalPath()->getRegex() <=> $a->getNormalPath()->getRegex();
        });

        return $route;
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
     * @return Match|null if a route is found or null else
     */
    public function match(Request $request)
    {
        if (!array_key_exists($request->getMethod(), $this->routes)) {
            throw new \InvalidArgumentException("[Router::match] method {$request->getMethod()} is not allowed");
        }

        foreach ($this->routes[$request->getMethod()] as $route) {

            $match = $route->match($request->getUri());
            if ($match !== null) {
                return $match;
            }
        }

        return null;
    }

    /**
     * @param string $name
     * @param array|null $parameters
     * @return Uri|null
     */
    public function build(string $name, ?array $parameters = [])
    {
        foreach ($this->routesMap as $routeName => $route) {
            if ($name === $routeName) {
                // apply arguments to the route
                // e.g '/test/{name: regex}' with '[name => 4]' becomes '/test/4'
                $rebuiltPath = $route->buildPath($parameters);
                if ($rebuiltPath !== null) {
                    return $rebuiltPath;
                }
            }
        }

        return null;
    }

}