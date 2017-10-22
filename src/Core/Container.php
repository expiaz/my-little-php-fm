<?php

namespace App\Core;

use ArrayAccess;

class Container implements ArrayAccess
{

    private $resolved;

    public function __construct()
    {
        $this->resolved = [];
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
     */
    public function get(string $key, $default = null)
    {
        if(! $this->has($key)){
            return $default;
        }

        $value = $this->resolved[$key];

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