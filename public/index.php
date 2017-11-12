<?php

require_once '../src/Core/constants/web.php';

require_once '../src/Core/constants/shared.php';

require_once ROOT . "vendor/autoload.php";

function debug($var){
    \App\Core\Utils\Debug::add($var);
}


$container = (new \App\Core\Bootstraper(CONFIG_FILE))->bootstrap();

if($container->get('config')->get('env') === 'dev'){
    ini_set('display_errors',1);
    error_reporting(E_ALL);
}

/**
 * @var $response \App\Core\Http\Response
 */
$response = $container->get(\App\Core\App::class)->run(\App\Core\Http\Request::fromGlobals());

/*$response = $container->get(\App\Core\App::class)->run(new \App\Core\Http\Request(
    \App\Core\Http\Request::GET,
    $container->get(\App\Core\Http\Router\Router::class)->build('category.list')->getFullUrl()
    ));*/

$response->send(true);

if($container->get('config')->get('env') === 'dev')
    \App\Core\Utils\Debug::print();

exit(0);