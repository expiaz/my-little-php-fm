<?php

namespace Tests\Core\Http\Router;

use App\Controller\TestController;
use App\Core\Http\Router\Route;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{

    public function testParsePath(){
        $route = new Route(
            'test.route',
            '/test/{var: \d+}',
            TestController::class . '::route'
        );

        self::assertContains('var', array_keys($route->getParameters()));
        self::assertEquals(1, count($route->getParameters()));

        self::assertEquals('~^\/test\/(\d+)$~', $route->getRegex());

    }

}