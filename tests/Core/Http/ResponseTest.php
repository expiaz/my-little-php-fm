<?php

namespace Tests\Core\Http;

use App\Core\Http\Response;
use App\Core\Http\Uri;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{

    public function testFilterStatus()
    {
        self::expectException(InvalidArgumentException::class);
        new Response(700);
    }

    public function testWithStatus()
    {
        $r = new Response();
        $r->withStatus(301);

        self::assertEquals(301, $r->getStatusCode());
        self::assertEquals('Moved Permanently', $r->getReasonPhrase());
    }

    public function testWithHeader()
    {
        $r = new Response();
        $r->withHeader(Response::LOCATION, 'test');

        self::assertTrue($r->hasHeader(Response::LOCATION));
    }

    public function testWrite()
    {
        $r = new Response();
        $r->write('bonjour');

        self::assertEquals('bonjour', $r->getBody());
    }

    public function testRedirect()
    {
        $r = new Response();
        $uri = new Uri('test');
        $r->withRedirect($uri);

        self::assertEquals(302,$r->getStatusCode());
        self::assertEquals('Found', $r->getReasonPhrase());
        self::assertTrue($r->hasHeader(Response::LOCATION));
        self::assertEquals($uri->getFullUrl(), $r->getHeader(Response::LOCATION));
    }

}