<?php

namespace App\Controller;

use App\Core\BaseController;

class TestController extends BaseController {

    public function indexAction(array $p): string
    {
        return 'test indexAction';
    }

    public function testAction(array $p): string
    {
        return "testAction $p[0]";
    }

}