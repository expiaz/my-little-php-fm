<?php

namespace Tests\Core;

use App\Core\Renderer;
use App\Core\View;
use PHPUnit\Framework\TestCase;

class RendererTest extends TestCase
{

    /**
     * @var Renderer
     */
    private $renderer;

    protected function setUp()
    {
        $this->renderer = new Renderer(TEST . 'View');
    }

    public function testResolvePathNsNotFound()
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessageRegExp("~\w* is not a valid namespace for a view~");
        $this->renderer->render('@doesNotExists/index');
    }

    public function testResolvePathFileNotFound(){
        $this->renderer->addNamespace('global', TEST);

        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessageRegExp("~[\w.\/]* is not a valid file for a view~");
        $this->renderer->render('@global/nothing');
    }

    public function testRender()
    {
        $this->renderer->addNamespace('bar', SRC . 'View');

        self::assertContains(
            'bar',
            $this->renderer->render('@bar/bar', [
                'foo' => 'bar'
            ])
        );
    }

}