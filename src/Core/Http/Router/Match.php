<?php

namespace App\Core\Http\Router;

use App\Core\Http\ParameterBag;

class Match extends ParameterBag
{

    private $route;

    public function __construct(Route $route)
    {
        parent::__construct();
        $this->route = $route;
    }

    public function addParameter(string $name, string $value)
    {
        $this->fields[$name] = $value;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->fields;
    }

    /**
     * @return Route
     */
    public function getRoute(): Route
    {
        return $this->route;
    }


}