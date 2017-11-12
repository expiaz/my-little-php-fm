<?php

require_once 'Core/constants/web.php';

require_once dirname(__DIR__) . '/src/Core/constants/shared.php';

define('TEST', ROOT . 'tests' . DS);
define('MODULE_TEST', TEST . 'Module' . DS);
define('TEST_CONFIG_FILE', MODULE_TEST . 'config.php');

require_once ROOT . "vendor/autoload.php";