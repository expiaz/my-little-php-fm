<?php

namespace App\Core\Http\Router;

use Core\App\Http\ParameterBag;

class Match extends ParameterBag
{

    private $route;

    public function __construct(Route $route)
    {
        parent::__construct();
        $this->route = $route;
    }

    public function addParameter(string $name, string $value){
        $this->fields[$name] = $value;
    }

    public function getParameters(){
        return $this->fields;
    }


}