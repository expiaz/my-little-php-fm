<?php

namespace App\Core\Utils;

class Stack
{

    /**
     * @var array
     * the stack implemented with an array
     */
    private $stack;

    public function __construct(?array $from = [])
    {
        $this->stack = $from;
    }

    /**
     * @return bool
     * Tests if this stack is empty.
     */
    public function empty(): bool
    {
        return count($this->stack) > 0;
    }

    /**
     * Looks at the object at the top of this stack without removing it from the stack.
     * @return mixed
     */
    public function peek()
    {
        return end($this->stack);
    }

    /**
     * Removes the object at the top of this stack and returns that object as the value of this function.
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->stack);
    }

    /**
     * @param $item
     * Pushes an item onto the top of this stack.
     */
    public function push($item)
    {
        array_push($this->stack, $item);
    }

    /**
     * @return int the size of the stack
     */
    public function length(): int
    {
        return count($this->stack);
    }

}