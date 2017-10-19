<?php

namespace App\Core\Http\Router;

use App\Core\Http\ParameterBag;

class Match
{

    private $route;
    private $parameters;

    public function __construct(Route $route, ParameterBag $parameterBag)
    {
        $this->route = $route;
        $this->parameters = $parameterBag;
    }

    /**
     * @return ParameterBag
     */
    public function getParameters(): ParameterBag
    {
        return $this->parameters;
    }

    /**
     * @return Route
     */
    public function getRoute(): Route
    {
        return $this->route;
    }


}