<?php

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__DIR__) . DS);
define('PUBLIK', ROOT . 'public' . DS);
define('SRC', ROOT . 'src' . DS);

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

//now let's call our frontController that'll send us the text response
$response = (new App\Core\Dispatcher\FrontController())->dispatch($_SERVER['REQUEST_URI'] ?? '');

echo $response;

exit(0);