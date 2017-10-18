<?php

namespace Tests\Core\Http\Router;

use App\Core\Http\Request;
use App\Core\Http\Router\Router;

class RouterTest
{

    public function testMatch(){

        $router = new Router();

        $router->get('/test/{id: \d+}', function (Request $request){
            return $request->getParam('id');
        });

        assertEquals('4', $router->match(new Request('GET', '/test/4')));
    }

    public function testBuild(){

        $router = new Router();

        $router->get('/test/{name: \w+}', function(Request $request){})->as('route.test');

        $response = $router->build('route.test', [
            'name' => 'john'
        ]);

        assertEquals($response->getUri(), '/test/john');
    }



}