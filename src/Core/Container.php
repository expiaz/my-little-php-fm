<?php

namespace App\Core;

use App\Core\Utils\Resolver;
use ArrayAccess;
use Exception;

class Container implements ArrayAccess
{

    /**
     * @var array
     */
    private $resolved;
    /**
     * @var Resolver
     */
    private $resolver;

    private static $instance = null;

    public static function getInstance(): Container {
        return self::$instance !== null
            ? self::$instance
            : self::$instance = new Container();
    }

    public function __construct()
    {
        $this->resolved = [];
        // set the container for DI
        $this->set(Container::class, $this);
        $this->resolver = new Resolver($this);
    }

    /**
     * @param callable $closure
     * @return mixed
     */
    public function singleton(callable $closure)
    {
        return call_user_func($closure, $this);
    }

    /**
     * @param callable $factory
     * @return callable
     */
    public function factory(callable $factory)
    {
        return $factory;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, $value): void
    {
        if(! $this->has($key)){
            $this->resolved[$key] = $value;
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->resolved[$key]);
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed
     * @throws Exception
     */
    public function get(string $key, $default = null)
    {
        // no key exists
        if(! $this->has($key)){
            // if it's a class
            if(class_exists($key)){
                try{
                    // try to resolve it
                    return $this->resolver->resolve($key);
                } catch (Exception $e){
                    // fail so return the default value provided
                    return $default;
                }
            }
            return $default;
        }

        $value = $this->resolved[$key];

        // is a closure
        if(is_callable($value)){
            return call_user_func($value, $this);
        }

        return $value;
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        if($this->has($offset)){
            unset($this->resolved[$offset]);
        }
    }
}