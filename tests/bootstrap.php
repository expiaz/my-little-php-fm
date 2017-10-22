<?php

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__DIR__) . DS);
define('PUBLIK', ROOT . 'public' . DS);
define('SRC', ROOT . 'src' . DS);
define('MODULE', SRC . 'Module' . DS);

define('TEST', ROOT . 'tests' . DS);
define('MODULE_TEST', TEST . 'Module' . DS);

define('WEBMETHOD', 'GET');
define('WEBHOST', 'test.com');
define('WEBSCHEME', 'http');
define('WEBROOT', WEBSCHEME . '://' . WEBHOST);
define('WEBURL', WEBROOT . '/test');

require_once ROOT . "vendor/autoload.php";