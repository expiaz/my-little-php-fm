<?php

namespace App\Core;

use App\Core\Utils\ParameterBag;
use App\Core\Http\Router\Router;

class Bootstraper
{
    private $config;

    public function __construct(string $configFile)
    {
        if(! file_exists($configFile)){
            throw new \InvalidArgumentException("$configFile does not exists");
        }

        $config = require($configFile);

        $this->config = new ParameterBag($config);
        foreach ($this->config->get('constants', []) as $constant => $value){
            define($constant, $value);
        }
    }

    public function bootstrap(): Container
    {
        $container = new Container();
        $renderer = new Renderer();
        $router = new Router();

        $renderer->addGlobal('renderer', $renderer);
        $renderer->addGlobal('router', $router);
        $renderer->addGlobal('config', $this->config);

        $container[Renderer::class] = $renderer;
        $container[Router::class] = $router;

        $container['config'] = $this->config;

        $app = new App($container, $this->config->get('modules', []));

        $container[App::class] = $app;

        return $container;
    }

}