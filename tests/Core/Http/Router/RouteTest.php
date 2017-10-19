<?php

namespace Tests\Core\Http\Router;

use App\Core\Http\Router\Route;
use PHPUnit\Framework\TestCase;
use Tests\Src\Controller\TestController;

class RouteTest extends TestCase
{

    public function testParsePath(){
        $route = new Route(
            'test.route',
            '/test/{var: \d+}',
            TestController::class . '::test'
        );

        self::assertContains('var', array_keys($route->getParameters()));
        self::assertEquals(1, count($route->getParameters()));

        self::assertEquals('~^\/test\/(\d+)$~', $route->getRegex());

    }

}