<?php

namespace Tests\Core\Http\Router;

use App\Core\Http\Request;
use App\Core\Http\Router\Match;
use App\Core\Http\Router\Router;
use App\Core\Http\Uri;
use PHPUnit\Framework\TestCase;
use Tests\Src\Controller\TestController;

class RouterTest extends TestCase
{

    public function testMatch(){

        $router = new Router();

        $router->get('/test/{id: \d+}', TestController::class . '::test', 'test.router');

        $request = new Request(Request::GET, '/test/4');

        $match = $router->match($request);

        self::assertInstanceOf(Match::class, $match);
        self::assertEquals('4', $match->getParameters()->get('id'));
        self::assertEquals(1, count($match->getParameters()));
        self::assertEquals('test.router', $match->getRoute()->getName());
        self::assertEquals('/test/{id: \d+}', $match->getRoute()->getPath());
    }

    public function testBuild(){

        $router = new Router();

        $router->get('/test/{name: \w+}', TestController::class . '::test', 'test.router');

        $uri = $router->build('test.router', [
            'name' => 'john'
        ]);

        self::assertInstanceOf(Uri::class, $uri);
        self::assertEquals('/test/john', $uri->getPath());
    }


    public function testOptionnals()
    {
        $router = new Router();
        $router->get('/test/{name: \w+}[/{opt: \d{2}}]', TestController::class . '::test', 'test.opt');

        $match = $router->match(new Request(Request::GET, '/test/jean'));

        self::assertInstanceOf(Match::class, $match);
        self::assertEquals('jean', $match->getParameters()->get('name'));
        self::assertEquals(1, count($match->getParameters()));
        self::assertEquals('test.opt', $match->getRoute()->getName());
        self::assertEquals('/test/{name: \w+}', $match->getRoute()->getPath());

        $uri = $router->build('test.opt', [
            'name' => 'john'
        ]);

        self::assertInstanceOf(Uri::class, $uri);
        self::assertEquals('/test/john', $uri->getPath());

        /**
         * OPTIONNAL PART
         */

        $match = $router->match(new Request(Request::GET, '/test/jean/45'));

        self::assertInstanceOf(Match::class, $match);
        self::assertEquals('jean', $match->getParameters()->get('name'));
        self::assertEquals('45', $match->getParameters()->get('opt'));
        self::assertEquals(2, count($match->getParameters()));
        self::assertEquals('test.opt', $match->getRoute()->getName());
        self::assertEquals('/test/{name: \w+}/{opt: \d{2}}', $match->getRoute()->getPath());

        $uri = $router->build('test.opt', [
            'name' => 'john',
            'opt' => 23
        ]);

        self::assertInstanceOf(Uri::class, $uri);
        self::assertEquals('/test/john/23', $uri->getPath());
    }

}