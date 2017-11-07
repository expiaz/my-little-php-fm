<?php

namespace Tests\Core\Utils;

use App\Core\Container;
use App\Core\Utils\Resolver;
use PHPUnit\Framework\TestCase;
use Tests\Module\Test\Controller\Test;

class ResolverTest extends TestCase {

    public function testResolve(){
        $container = new Container();
        self::assertInstanceOf(Test::class, $container->get(Test::class));
    }

}