<?php

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(dirname(__DIR__))) . DS);
define('PUBLIK', ROOT . 'public' . DS);
define('SRC', ROOT . 'src' . DS);
define('MODULE', SRC . 'Module' . DS);

define('STYLES', WEBROOT . '/assets/style/');
define('SCRIPTS', WEBROOT . '/assets/script/');

define('CONFIG_FILE', MODULE . 'config.php');