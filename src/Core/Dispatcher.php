<?php

namespace App\Core;

use App\Core\Exception\ClassNotFoundException;
use App\Core\Exception\MethodNotFoundException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use InvalidArgumentException;

class Dispatcher
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * parse and resolve $handler (such as Ns\Class::method become [Ns\Class(), method])
     * @param string $handler
     * @return array
     * @throws ClassNotFoundException
     * @throws MethodNotFoundException
     */
    private function resolve(string $handler): array
    {
        if (($pos = strpos($handler, '::')) === false) {
            throw new InvalidArgumentException("[Dispatcher::resolve] $handler is not a valid handler");
        }

        $controller = substr($handler, 0, $pos);
        $action = substr($handler, $pos + 2);

        if (!class_exists($controller)) {
            throw new ClassNotFoundException("$controller does not exists", $controller);
        }

        $controllerInstance = new $controller($this->container);

        //let's now ensure that the method exists
        if (!method_exists($controllerInstance, $action)) {
            throw new MethodNotFoundException("$action does not exists in $controller", $action);
        }

        return [$controller, $action];
    }

    /**
     * resolve then dispatch the given request to the handler, send back the response from the handler
     * @param string|callable $handler if string : Ns\Class::method
     * @param Request $request
     * @return Response
     */
    public function dispatch($handler, Request $request): Response
    {
        if (is_callable($handler)) {
            return call_user_func($handler, $request);
        }

        $pieces = $this->resolve($handler);

        if (count($pieces) === 0) {
            throw new InvalidArgumentException("$handler is not a valid handler");
        }

        $controller = $pieces[0];
        $action = $pieces[1];

        return $controller->$action($request);
    }
}