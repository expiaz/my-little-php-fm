<?php

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__DIR__) . DS);
define('PUBLIK', ROOT . 'public' . DS);
define('SRC', ROOT . 'src' . DS);
define('MODULE', SRC . 'Module' . DS);

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

define('DEBUG', true);

require_once ROOT . "vendor/autoload.php";

function debug($var){
    \App\Core\Utils\Debug::add($var);
}

$container = (new \App\Core\Bootstraper(
    \App\Module\Site\Controller\SiteController::MODULE_PATH . 'config.php'
    ))->bootstrap();

/**
 * @var $response \App\Core\Http\Response
 */
$response = $container->get(\App\Core\App::class)->run(\App\Core\Http\Request::fromGlobals());

$response->send(true);

if(DEBUG)
    \App\Core\Utils\Debug::print();

exit(0);