<?php

namespace App\Core;

abstract class BaseController
{
    protected $renderer;

    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    protected function getParam(array $parameters, int $index = 0, $default = null){
        return count($parameters) >= ($index + 1)
            ? $parameters[$index]
            : $default;
    }

    public abstract function indexAction(array $parameters): string;

}