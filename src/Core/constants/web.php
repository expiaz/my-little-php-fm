<?php

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