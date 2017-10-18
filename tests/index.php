<?php


use App\Core\Http\Request;
use App\Core\Http\Router\Router;

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__DIR__) . DS);
define('PUBLIK', ROOT . 'public' . DS);
define('SRC', ROOT . 'src' . DS);

define('WEBHOST', 'test.com');
define('WEBSCHEME', 'http');
define('WEBROOT', WEBSCHEME . '://' . WEBHOST);
define('WEBURL', WEBROOT . '/test');

require_once ROOT . "vendor/autoload.php";