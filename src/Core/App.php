<?php

namespace App\Core;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Router\Router;
use InvalidArgumentException;

class App
{
    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container, ?array $modules = [])
    {
        foreach ($modules as $module){
            if(is_callable($module . '::register')){
                call_user_func_array(
                    $module . '::register',
                    [
                        $container,
                        $container->get(Router::class),
                        $container->get(Renderer::class)
                    ]
                );
            }
        }

        $this->container = $container;
    }

    public static function handle(string $url, ?string $method = WEBMETHOD): Response
    {
        return (new self())->run(new Request($method, $url));
    }

    public function run(Request $request): Response
    {
        /**
         * @var $router Router
         */
        $router = $this->container->get(Router::class);
        $match = $router->match($request);

        if($match === null){
            return new Response(404, [], $this->container->get(Renderer::class)->render('/error/404'));
        }

        $dispatcher = new Dispatcher($this->container);

        return $dispatcher->dispatch($match->getRoute()->getHandler(), Request::fromMatch($match));
    }

}