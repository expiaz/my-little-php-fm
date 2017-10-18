<?php

namespace Tests\Core;

use App\Core\View;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{

    public function testMakePath(){
        self::assertEquals(SRC . 'View/test.php', (new View('test'))->getTemplate());
    }

    public function testRender(){
        self::assertContains('bar', (new View('test'))->render(['foo' => 'bar']));
    }

}