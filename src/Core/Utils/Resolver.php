<?php

namespace App\Core\Utils;

use App\Core\Container;
use Exception;
use ReflectionClass;
use ReflectionParameter;

class Resolver
{
    /**
     * @var Container
     */
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
    public function resolveHandler(string $handler): array
    {
        if (($pos = strpos($handler, '::')) === false) {
            throw new InvalidArgumentException("[Dispatcher::resolve] $handler is not a valid handler");
        }

        $controller = substr($handler, 0, $pos);
        $action = substr($handler, $pos + 2);

        if (! class_exists($controller)) {
            throw new ClassNotFoundException("$controller does not exists", $controller);
        }

        //let's now ensure that the method exists
        if (!method_exists($controller, $action)) {
            throw new MethodNotFoundException("$action does not exists in $controller", $action);
        }

        return [$controller, $action];
    }

    /**
     * try to resolve the arguments of the constructor
     * of a namespaced class and return an instance of it
     * @param string $class
     * @return mixed the resolved class
     */
    public function resolve(string $class)
    {
        // there is already a key for this class
        if($this->container->has($class)){
            return $this->container->get($class);
        }

        $reflection = new ReflectionClass($class);
        // if there's a constructor and dependencies for it
        if ($reflection->getConstructor() !== null && count($reflection->getConstructor()->getParameters())) {
            // retrieve the parameters of the constructor
            $parameters = $reflection->getConstructor()->getParameters();
            $resolved = [];
            // resolve the parameter
            foreach ($parameters as $parameter) {
                $resolved[] = $this->getValue($parameter);
            }
            $instance = new $class(... $resolved);
        } else {
            $instance = new $class();
        }

        $this->container->set($class, $instance);

        return $instance;
    }

    /**
     * @param ReflectionParameter $parameter
     * @return mixed|null
     * @throws Exception
     */
    private function getValue(ReflectionParameter $parameter)
    {
        // if parameter if not resolvable
        if (!$parameter->hasType() && !$parameter->allowsNull()) {
            throw new Exception("Can't resolve $parameter, no type, no null");
        }

        // type is a builtin, not resolvable is there's no default value provided
        if ($parameter->getType()->isBuiltin() && $parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        try {
            // try to resolve the given parameter
            return $this->resolve($parameter->getType()->getName());
        } catch (Exception $e) {
            // fail, there is default value provided ?
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }
            // does it allows null ?
            if ($parameter->allowsNull() || $parameter->getType()->allowsNull()) {
                return null;
            }
            throw $e;
        }

    }

}