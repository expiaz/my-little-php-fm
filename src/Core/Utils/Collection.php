<?php

namespace App\Core\Utils;

class Collection implements \ArrayAccess
{

    private $array;

    public function __construct(?array $from = [])
    {
        $this->array = $from;
    }

    /**
     * @return int
     */
    public function length()
    {
        return count($this->array);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->length() === 0;
    }

    /**
     * @param $element
     * @return int
     */
    public function indexOf($element): int
    {
        $index = array_search($element, $this->array);
        return $index === false ? -1 : $index;
    }

    /**
     * @return mixed
     */
    public function first()
    {
        return $this->isEmpty() ? null : $this->array[0];
    }

    /**
     * @return mixed
     */
    public function last()
    {
        return $this->isEmpty() ? null : end($this->array);
    }

    /**
     * @param int $index
     * @return mixed
     */
    public function has(int $index)
    {
        return isset($this->array[$index]);
    }

    /**
     * @param int $index
     * @return mixed
     */
    public function get(int $index)
    {
        return $this->has($index) ? $this->array[$index] : null;
    }

    /**
     * @param int $index
     * @param $value
     * @return void
     */
    public function set(int $index, $value): void
    {
        $this->array[$index] = $value;
    }

    /**
     * @return array
     */
    public function asArray(): array
    {
        return $this->array;
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
        unset($this->array[$offset]);
    }
}