<?php

namespace App\Controller;

use App\Core\BaseController;

class IndexController extends BaseController
{

    public function indexAction(array $p): string
    {
        return $this->renderer->render('index', [
            'h1' => 'Site SIL3'
        ]);
    }

    public function infoAction(array $p): string
    {
        return $this->renderer->render('informations');
    }


}