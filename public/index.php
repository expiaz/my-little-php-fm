<?php

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__DIR__) . DS);
define('PUBLIK', ROOT . 'public' . DS);
define('SRC', ROOT . 'src' . DS);

define('WEBMETHOD', strtoupper($_SERVER['REQUEST_METHOD']));
define('WEBHOST', $_SERVER['HTTP_HOST']);
define('WEBSCHEME',
    (
        array_key_exists('HTTPS', $_SERVER)
        && ! empty($_SERVER['HTTPS'])
        && $_SERVER['HTTPS'] !== 'off'
    )
    ? 'https'
    : 'http'
);
define('WEBROOT', WEBSCHEME . '://' . WEBHOST);
define('WEBURL', WEBROOT . $_SERVER['REQUEST_URI'] ?? '');

require_once ROOT . "vendor/autoload.php";

$container = new \App\Core\Container();
$renderer = new \App\Core\Renderer(SRC . 'View');
$router = new \App\Core\Http\Router\Router();

$renderer->addGlobal('renderer', $renderer);
$renderer->addGlobal('router', $router);

$container[\App\Core\Renderer::class] = $renderer;
$container[\App\Core\Http\Router\Router::class] = $router;

$app = new \App\Core\App($container, [
    \App\Controller\IndexController::class
]);
$container[\App\Core\App::class] = $app;

$response = $app->run(\App\Core\Http\Request::fromGlobals());

$response->send(true);

exit(0);