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
        $container->set(DbImageDAO::class, new DbImageDAO($container));

        $renderer->addNamespace('image', self::MODULE_PATH . 'View');

        $router->get('/image/first[/{nb: \d+}]', ImageController::class . '::firstAction', 'image.first');
        $router->get('/image[/{image: \d+}]', ImageController::class . '::showAction', 'image.show');
        $router->get('/image/grid[/{image: \d+}[/{nb: \d+}]]', ImageController::class . '::gridAction', 'image.grid');
        $router->get('/image/jump/{forward: \d{1}}/{image: \d+}[/{nb: \d+}]', ImageController::class . '::jumpAction', 'image.jump');
        $router->get('/image/random/[{image: \d+}[/{nb: \d+}]]', ImageController::class . '::randomAction', 'image.random');
    }

    private $dao;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->dao = $container->get(DbImageDAO::class);
    }

    public function firstAction(Request $request): Response
    {
        $nb = $request->getParameters()->get('nb');

        // grid mode
        if($nb !== null){
            return (new Response())->withRedirect($this->router->build('image.grid', [
                'nb' => $nb,
                'image' => $this->dao->getFirstImage()->getId()
            ]));
        }

        // single mode
        return (new Response())->withRedirect($this->router->build('image.show', [
            'image' => $this->dao->getFirstImage()->getId()
        ]));
    }

    public function jumpAction(Request $request): Response
    {
        $forward = (int)$request->getParameters()->get('forward');
        $image = $this->dao->getImage($request->getParameters()->get('image'));
        $nb = $request->getParameters()->get('nb');

        // jump to grid
        if ($nb !== null) {
            return (new Response())->withRedirect($this->router->build('image.grid', [
                'image' => $this->dao->jumpToImage($image, $forward ? $nb : -$nb)->getId(),
                'nb' => $nb
            ]));
        }

        // jump to single
        return (new Response())->withRedirect($this->router->build('image.show', [
            'image' => ($forward ? $this->dao->getNextImage($image) : $this->dao->getPrevImage($image))->getId(),
        ]));
    }

    public function randomAction(Request $request): Response
    {
        $image = $this->dao->getRandomImage();
        $nb = $request->getParameters()->get('nb');

        // jump to grid
        if ($nb !== null) {
            return (new Response())->withRedirect($this->router->build('image.grid', [
                'image' => $this->dao->jumpToImage($image, $nb)->getId(),
                'nb' => $nb
            ]));
        }

        return (new Response())->withRedirect($this->router->build('image.show', [
            'image' => $image->getId()
        ]));
    }


    public function gridAction(Request $request): Response
    {
        $image = $this->dao->getImage($request->getParameters()->get('image', $this->dao->getFirstImage()->getId()));
        $nb = (int)$request->getParameters()->get('nb', 2);

        $images = $this->dao->getImageList($image, $nb);

        return new Response(200, [], $this->renderer->render('@image/grid', [
            'image' => $image,
            'images' => $images,
            'nb' => $nb,
            'nextNb' => $nb * 2
        ]));
    }

    public function showAction(Request $request): Response
    {
        $image = $this->dao->getImage(
            $request->getParameters()->get('image', $this->dao->getFirstImage()->getId())
        );

        return new Response(200, [], $this->renderer->render('@image/show', [
            'image' => $image
        ]));
    }


}