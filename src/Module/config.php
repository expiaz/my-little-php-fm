<?php

return [
    'env' => 'dev',

    'image' => [
        'path' => PUBLIK . 'assets/img',
        'url' => WEBROOT . '/assets/img',
        'upload' => PUBLIK . 'assets/img/upload/'
    ],

    'database' => [
        'path' => \App\Module\Image\Controller\ImageController::MODULE_PATH . 'ressources/images.db',
        'user' => null,
        'pwd' => null
    ],

    'modules' => [
        \App\Module\Site\Controller\SiteController::class,
        \App\Module\Image\Controller\ImageController::class,
        \App\Module\Category\Controller\CategoryController::class,
        \App\Module\User\Controller\UserController::class,
    ]
];