<?php

namespace Tests\Module\Image\Model\Repository;


use App\Core\Bootstraper;
use App\Core\Container;
use App\Module\Image\Model\Entity\Image;
use App\Module\Image\Model\Repository\AbstractDAO;
use App\Module\Image\Model\Repository\DbImageDAO;
use PHPUnit\Framework\TestCase;

class ImageDAOTest extends TestCase
{

    /**
     * @var DbImageDAO
     */
    private $dao;

    /**
     * @var Container
     */
    private $container;

    protected function setUp()
    {
        $boot = new Bootstraper(TEST_CONFIG_FILE);
        $this->container = $boot->bootstrap();
        $this->dao = new DbImageDAO($this->container);
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

        $files = DbImageDAO::scanDir($this->container->get('config')->get('image.path'));

        self::assertContains(
            "/pictures/airshow_23_bg_101400.jpg",
            $files
        );
    }

}