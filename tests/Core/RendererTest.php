<?php

namespace Tests\Core;

use App\Core\Container;
use App\Core\Http\Router\Router;
use App\Core\Renderer;
use App\Core\View;
use App\Module\Site\Controller\SiteController;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Tests\Module\Test\Controller\TestController;

class RendererTest extends TestCase
{

    /**
     * @var Renderer
     */
    private $renderer;

    protected function setUp()
    {
        $this->renderer = new Renderer(TestController::MODULE_PATH . 'View');
    }

    public function testResolvePathNsNotFound()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessageRegExp("~\w* is not a valid namespace for a view~");
        $this->renderer->render('@doesNotExists/index');
    }

    public function testResolvePathFileNotFound()
    {
        $this->renderer->addNamespace('global', TEST);

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessageRegExp("~[\w.\/]* is not a valid file for a view~");
        $this->renderer->render('@global/nothing');
    }

    public function testRender()
    {
//        TestController::register(new Container(), new Router(), $this->renderer);

        $this->renderer->addNamespace('test', TestController::MODULE_PATH . 'View');
//        $this->renderer->addNamespace('site', SiteController::MODULE_PATH . 'View');

        $content = $this->renderer->render('@test/test', [
            'name' => 'foo',
            'opt' => 'default'
        ]);

        self::assertContains(
            'foo',
            $content
        );
    }

    public function testContext()
    {
        $this->renderer->addNamespace('test', TestController::MODULE_PATH . 'View');

        $content = $this->renderer->render('@test/test', [
            'name' => 'name_context1',
            'opt' => 'opt_context1'
        ]);

        self::assertEquals(0, count($this->renderer->getCurrentContext()->asArray()));
        self::assertContains('name_context2', $content);
        self::assertContains('opt_context1', $content);
    }

}