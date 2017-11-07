<?php

namespace Tests\Core\Utils;

use App\Core\Utils\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase {

    /**
     * @var Collection
     */
    private $collection;

    protected function setUp()
    {
        $this->collection = new Collection([
            1,
            2,
            3
        ]);
    }

    public function testFirst(){
        self::assertEquals(1, $this->collection->first());
    }

    public function testLast(){
        self::assertEquals(3, $this->collection->last());
    }

    public function testIndexOf(){
        self::assertEquals(0, $this->collection->indexOf(1));
        self::assertEquals(1, $this->collection->indexOf(2));
        self::assertEquals(2, $this->collection->indexOf(3));
    }

    public function testIndexOfOob(){
        self::assertEquals(-1, $this->collection->indexOf(4));
    }

}