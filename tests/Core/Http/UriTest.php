<?php

namespace Tests\Core\Http;

use App\Core\Http\Uri;
use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{

    public function testExtractParts()
    {
        $uri = new Uri('http://test.com:8080/some/path?a=b#c');

        self::assertEquals('http', $uri->getScheme());
        self::assertEquals('test.com', $uri->getHost());
        self::assertEquals('8080', $uri->getPort());
        self::assertEquals('/some/path', $uri->getPath());
        self::assertEquals('a=b', $uri->getQuery());
        self::assertEquals('c', $uri->getFragment());
    }

    public function testExtractPartsDefaults(){

        $uri = new Uri('test.com');

        self::assertEquals('http', $uri->getScheme());
        self::assertEquals('test.com', $uri->getHost());
        self::assertEquals('80', $uri->getPort());
        self::assertEquals('/', $uri->getPath());
        self::assertEquals('', $uri->getQuery());
        self::assertEquals('', $uri->getFragment());

        $uri = new Uri('/test');

        self::assertEquals('http', $uri->getScheme());
        self::assertEquals('test.com', $uri->getHost());
        self::assertEquals('80', $uri->getPort());
        self::assertEquals('/test', $uri->getPath());
        self::assertEquals('', $uri->getQuery());
        self::assertEquals('', $uri->getFragment());
    }

}