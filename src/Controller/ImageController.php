<?php

namespace App\Controller;

use App\Core\BaseController;
use App\Core\Renderer;
use App\Model\Dao\DbImageDAO;

class ImageController extends BaseController
{

    private $dao;

    public function __construct(Renderer $renderer)
    {
        parent::__construct($renderer);
        $this->dao = new DbImageDAO();
    }

    public function indexAction(array $parameters): string
    {
        $img = count($parameters) && is_numeric($parameters[0])
            ? $this->dao->getImage((int)$parameters[0])
            : $this->dao->getFirstImage();

        $size = count($parameters) >= 2 && is_numeric($parameters[1])
            ? (int)$parameters[1]
            : 480;

        return $this->renderer->render('image/single', [
            'img' => $img,
            'size' => $size
        ]);
    }

    public function zoomAction(array $p): string
    {

        $zoom = (int)$this->getParam($p, 0, 0);
        $id = (int)$this->getParam($p, 1, (new DbImageDAO())->getFirstImage()->getId());
        $size = (int)$this->getParam($p, 2, 480);

        switch ($zoom) {
            case 0:
                $size = (int)($size * 0.75);
                break;
            case 1:
                $size = (int)($size * 1.25);
                break;

            default:
                break;
        }

        ++$size;

        header('Location: ' . WEBROOT . "/image/single/$id/$size");

        return "";
    }

    public function firstAction(array $p): string
    {
        header('Location: ' . WEBROOT . "/image/single/{$this->dao->getFirstImage()->getId()}/{$this->getParam($p, 0, 480)}");

        return "";
    }

    public function randomAction(array $p): string
    {
        header('Location: ' . WEBROOT . "/image/single/{$this->dao->getRandomImage()->getId()}/{$this->getParam($p, 0, 480)}");

        return "";
    }

    public function moreAction(array $parameters): string
    {
        $img = count($parameters) && is_numeric($parameters[0])
            ? $this->dao->getImage((int)$parameters[0])
            : $this->dao->getFirstImage();
        $size = (int)$this->getParam($parameters, 1, 480);
        $nb = (int)$this->getParam($parameters, 2, 2);

        $images = $this->dao->getImageList($img, $nb);
        $columnSize = (int) ($size / sqrt(count($images)));

        return $this->renderer->render('image/grid', [
            'img' => $img,
            'images' => $images,
            'size' => $size,
            'columnSize' => $columnSize,
            'nb' => $nb
        ]);
    }

    public function prevAction(array $parameters): string
    {
        $img = count($parameters) && is_numeric($parameters[0])
            ? $this->dao->getImage((int)$parameters[0])
            : $this->dao->getFirstImage();
        $size = (int)$this->getParam($parameters, 1, 480);
        $nb = $this->getParam($parameters, 2);

        //grid
        if($nb !== null){
            $nb = (int)$nb;
            $location = WEBROOT . "/image/more/{$this->dao->jumpToImage($img, -$nb)->getId()}/$size/$nb";
        } else {
            //single
            $location = WEBROOT . "/image/single/{$this->dao->getPrevImage($img)->getId()}/$size";
        }

        header('Location: ' . $location);

        return "";
    }

    public function nextAction(array $parameters): string
    {
        $img = count($parameters) && is_numeric($parameters[0])
            ? $this->dao->getImage((int)$parameters[0])
            : $this->dao->getFirstImage();
        $size = (int)$this->getParam($parameters, 1, 480);
        $nb = $this->getParam($parameters, 2);

        //grid
        if($nb !== null){
            $nb = (int)$nb;
            $location = WEBROOT . "/image/more/{$this->dao->jumpToImage($img, $nb)->getId()}/$size/$nb";
        } else {
            //single
            $location = WEBROOT . "/image/single/{$this->dao->getNextImage($img)->getId()}/$size";
        }

        header('Location: ' . $location);

        return "";
    }

}