<?php

namespace App\Core\Http\Router;

use App\Core\Http\ParameterBag;

class Match extends ParameterBag
{

    private $route;
    private $path;

    public function __construct(Route $route, Path $path)
    {
        parent::__construct();
        $this->route = $route;
        $this->path = $path;
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

    /**
     * @return Path
     */
    public function getPath(): Path
    {
        return $this->path;
    }


}