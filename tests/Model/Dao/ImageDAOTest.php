<?php

namespace Tests\Model\Dao;

use App\Model\Dao\AbstractDAO;
use App\Model\Dao\DbImageDAO;

use App\Model\Entity\Image;
use PHPUnit\Framework\TestCase;

class ImageDAOTest extends TestCase
{

    /**
     * @var DbImageDAO
     */
    private $dao;

    protected function setUp()
    {
        $this->dao = new DbImageDAO();
    }

    public function testGetImage()
    {
        self::assertInstanceOf(Image::class, $this->dao->getImage(1));
        self::assertEquals(1, $this->dao->getImage(1)->getId());

        //OOBs
        self::assertEquals(1, $this->dao->getImage(-54)->getId());
        self::assertEquals($this->dao->size(), $this->dao->getImage(999999)->getId());
    }

    public function testReadDir()
    {

        $files = AbstractDAO::scanDir(PUBLIK . 'assets/img');

        self::assertContains(
            "/pictures/airshow_23_bg_101400.jpg",
            $files
        );
    }

}