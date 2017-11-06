<?php

namespace App\Core;

use App\Core\Utils\ParameterBag;
use App\Core\Http\Router\Router;
use App\Module\Image\Model\Repository\DbImageDAO;
use PDO;

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
        $container[PDO::class] = new PDO(
            "sqlite:" . DbImageDAO::DB_PATH,
            null,
            null,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
            ]
        );

        $container['config'] = $this->config;

        $app = new App($container, $this->config->get('modules', []));

        $container[App::class] = $app;

        return $container;
    }

}