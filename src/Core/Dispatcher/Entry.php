<?php

namespace App\Core\Dispatcher;

class Entry
{
    public $controller;
    public $action;
    public $parameters;

    public function __construct(
        string $controller = FrontController::DEFAULT_CONTROLLER,
        string $action = FrontController::DEFAULT_ACTION,
        array $parameters = []
    )
    {
        $this->controller = $controller;
        $this->action = $action;
        $this->parameters = $parameters;
    }
}