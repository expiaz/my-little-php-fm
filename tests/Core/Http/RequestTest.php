<?php

namespace Tests\Core\Http;

use App\Core\Http\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase {

    public function testGuessMethod(){
        $request = new Request(Request::GET, '/test');

        self::assertEquals(Request::GET, $request->getMethod());
    }

}