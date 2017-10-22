<?php

namespace App\Module\Image\Controller;

use App\Core\BaseController;
use App\Core\Container;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Router\Router;
use App\Core\Renderer;
use App\Module\Image\Model\Repository\DbImageDAO;
use App\Module\Site\Controller\SiteController;

class ImageController extends BaseController
{

    public const MODULE_PATH = MODULE . 'Image/';

    /**
     * enable route and views registering, called from a static context (no $this)
     * @param Container $container
     * @param Router $router
     * @param Renderer $renderer
     * @return void
     */
    public static function register(Container $container, Router $router, Renderer $renderer): void
    {
        $renderer->addNamespace('image', self::MODULE_PATH . 'View');

        $router->get('/image[/{id:\d+}[/{size: \d+}]]', ImageController::class . '::showAction', 'image.show');
        $router->get('/image/grid/{id: \d+}/{size: \d+}[/{nb: \d+}]', ImageController::class . '::gridAction', 'image.grid');
        $router->get('/image/jump/{forward: \d{1}}/{id:\d+}[/{size: \d+}[/{nb: \d+}]]', ImageController::class . '::jumpAction', 'image.jump');
        $router->get('/image/zoom/{zoom: \d{1}}/{id: \d+}/{size: \d+}', ImageController::class . '::zoomAction', 'image.zoom');
        $router->get('/image/random/[{id: \d+}[/{size: \d+}[/{nb: \d+}]]]', ImageController::class . '::randomAction', 'image.random');
    }

    private $dao;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->dao = new DbImageDAO();
    }

    public function showAction(Request $request): Response
    {
        $img = $this->dao->getImage(
            $request->getParameters()->get('id', $this->dao->getFirstImage()->getId())
        );
        $size = $request->getParameters()->get('size', 480);

        return new Response(200, [], $this->renderer->render('@image/show', [
            'img' => $img,
            'size' => $size
        ]));
    }

    public function gridAction(Request $request): Response
    {
        $img = $this->dao->getImage($request->getParameters()->get('id'));
        $size = (int)$request->getParameters()->get('size', 480);
        $nb = (int)$request->getParameters()->get('nb', 2);

        $images = $this->dao->getImageList($img, $nb);
        $columnSize = (int)($size / sqrt(count($images)));

        return new Response(200, [], $this->renderer->render('@image/grid', [
            'img' => $img,
            'images' => $images,
            'columnSize' => $columnSize,
            'size' => $size,
            'nb' => $nb,
            'nextNb' => $nb * 2
        ]));
    }

    public function jumpAction(Request $request): Response
    {
        $forward = (int)$request->getParameters()->get('forward');
        $img = $this->dao->getImage($request->getParameters()->get('id'));
        $size = (int)$request->getParameters()->get('size', 480);
        $nb = $request->getParameters()->get('nb');

        // jump to grid
        if ($nb !== null) {
            return (new Response())->withRedirect($this->router->build('image.grid', [
                'id' => $this->dao->jumpToImage($img, $forward ? $nb : -$nb)->getId(),
                'size' => $size,
                'nb' => $nb
            ]));
        }

        // jump to single
        return (new Response())->withRedirect($this->router->build('image.show', [
            'id' => ($forward ? $this->dao->getNextImage($img) : $this->dao->getPrevImage($img))->getId(),
            'size' => $size
        ]));
    }

    public function zoomAction(Request $request): Response
    {
        $zoom = (int)$request->getParameters()->get('zoom', 0);
        $img = $this->dao->getImage($request->getParameters()->get('id'));
        $size = (int)$request->getParameters()->get('size', 480);

        $size = (int)($size * ($zoom ? 1.25 : 0.75));

        if ($size < 2) {
            $size = 2;
        }

        return (new Response())->withRedirect($this->router->build('image.show', [
            'id' => $img->getId(),
            'size' => $size
        ]));
    }

    public function randomAction(Request $request): Response
    {
        $id = $request->getParameters()->get('id');
        $img = $this->dao->getRandomImage();
        $size = (int)$request->getParameters()->get('size', 480);
        $nb = $request->getParameters()->get('nb');

        if ($id !== null) {
            $actual = $this->dao->getImage($id);

            while ($actual->getId() === $img->getId()) {
                $img = $this->dao->getRandomImage();
            }
        }

        // jump to grid
        if ($nb !== null) {
            return (new Response())->withRedirect($this->router->build('image.grid', [
                'id' => $this->dao->jumpToImage($img, $nb)->getId(),
                'size' => $size,
                'nb' => $nb
            ]));
        }

        return (new Response())->withRedirect($this->router->build('image.show', [
            'id' => $img->getId(),
            'size' => $size
        ]));
    }


}