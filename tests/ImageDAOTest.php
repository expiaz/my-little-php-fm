<?php

namespace Tests;

use App\Model\AbstractDAO;
use App\Model\DbImageDAO;
use App\Model\Image;
use App\Model\ImageDAO;
use PHPUnit\Framework\TestCase;

class ImageDAOTest extends TestCase{

    /**
     * @var ImageDAO
     */
    private $dao;

    protected function setUp()
    {
        $this->dao = new DbImageDAO();
    }

    public function testGetImage(){
        self::assertInstanceOf(Image::class, $this->dao->getImage(1));
        self::assertEquals(1, $this->dao->getImage(1)->getId());

        //OOBs
        self::assertEquals(1, $this->dao->getImage(-54)->getId());
        self::assertEquals($this->dao->size(), $this->dao->getImage(999999)->getId());
    }

    public function testReadDir(){
        $files = AbstractDAO::scanDir(dirname(__DIR__) . "/src/Model/IMG");

        self::assertContains(
            "/jons/pictures/airshow_23_bg_101400.jpg",
            $files
        );
    }

}