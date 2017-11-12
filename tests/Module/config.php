<?php
return array_merge(
    require CONFIG_FILE,
    [
        'modules' => [
            \Tests\Module\Test\Controller\TestController::class
        ]
    ]
);