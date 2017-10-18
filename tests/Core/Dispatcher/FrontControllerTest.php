<?php

namespace Tests\Core\Dispatcher;

use App\Core\Dispatcher\Entry;
use App\Core\Dispatcher\FrontController;
use PHPUnit\Framework\TestCase;

class FrontControllerTest extends TestCase
{

    /**
     * @var FrontController
     */
    private $test;

    protected function setUp()
    {
        $this->test = new FrontController();
    }

    public function testExtractRessource()
    {
        /**
         * SCHEME
         */

        $uri = 'http://monsite.com/controller/action/p1/p2';
        $extracted = $this->test->extractRessource($uri);
        self::assertEquals("controller/action/p1/p2", $extracted);
        self::assertEquals("controller/action/p1/p2", $extracted);

        $uri = 'http://monsite.com/controller/action/p1/p2/';
        $extracted = $this->test->extractRessource($uri);
        self::assertEquals("controller/action/p1/p2", $extracted);

        $uri = 'http://monsite.com/';
        $extracted = $this->test->extractRessource($uri);
        self::assertEquals("/", $extracted);

        $uri = 'http://monsite.com';
        $extracted = $this->test->extractRessource($uri);
        self::assertEquals("/", $extracted);

        /**
         * W/O SCHEME
         */

        $uri = '/controller/action/p1/p2';
        $extracted = $this->test->extractRessource($uri);
        self::assertEquals("controller/action/p1/p2", $extracted);

        $uri = '/controller/action/p1/p2/';
        $extracted = $this->test->extractRessource($uri);
        self::assertEquals("controller/action/p1/p2", $extracted);

        $uri = '/';
        $extracted = $this->test->extractRessource($uri);
        self::assertEquals("/", $extracted);

        $uri = '';
        $extracted = $this->test->extractRessource($uri);
        self::assertEquals("/", $extracted);
    }

    public function testParse()
    {
        $entry = $this->test->parse('/controller/action/p1/p2');

        self::assertEquals('controller', $entry->controller);
        self::assertEquals('action', $entry->action);
        self::assertContains('p1', $entry->parameters);
        self::assertContains('p2', $entry->parameters);
        self::assertEquals(count($entry->parameters), 2);

        $entry = $this->test->parse('/controller/action');

        self::assertEquals('controller', $entry->controller);
        self::assertEquals('action', $entry->action);
        self::assertEquals(count($entry->parameters), 0);

        $entry = $this->test->parse('/controller');

        self::assertEquals('controller', $entry->controller);
        self::assertEquals(FrontController::DEFAULT_ACTION, $entry->action);
        self::assertEquals(count($entry->parameters), 0);

        $entry = $this->test->parse('/');

        self::assertEquals(FrontController::DEFAULT_CONTROLLER, $entry->controller);
        self::assertEquals(FrontController::DEFAULT_ACTION, $entry->action);
        self::assertEquals(count($entry->parameters), 0);
    }

    public function testLoad()
    {
        $entry = new Entry('test', 'test', [1, 2]);
        $response = $this->test->load($entry);
        self::assertEquals("testAction 1", $response);

        $entry = new Entry('test', 'methodNotFound');
        $response = $this->test->load($entry);
        self::assertEquals("test indexAction", $response);

        $entry = new Entry('controllerNotFound', 'methodNotFound');
        $response = $this->test->load($entry);
        self::assertTrue(is_string($response));

        $entry = new Entry();
        $response = $this->test->load($entry);
        self::assertTrue(is_string($response));

        self::assertEquals($response, $response);
    }

    /*public function testLoadFail(){
        $this->expectException(ClassNotFoundException::class);
        $this->test->load(new Entry('it', 'does', ['not exists']));
    }*/

}