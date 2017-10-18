<?php

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__DIR__) . DS);
define('PUBLIK', ROOT . 'public' . DS);
define('SRC', ROOT . 'src' . DS);

define('WEBHOST', 'monsite.com');
define('WEBROOT', 'http://' . WEBHOST);
define('WEBURL', WEBROOT . '/test');

require_once ROOT . "vendor/autoload.php";