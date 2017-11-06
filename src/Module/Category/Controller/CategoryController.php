<?php

namespace App\Module\Category\Controller;

use App\Core\BaseController;
use App\Core\Container;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Router\Router;
use App\Core\Renderer;
use App\Module\Category\Model\Repository\CategoryDAO;
use App\Module\Image\Model\Repository\DbImageDAO;

class CategoryController extends BaseController
{

    public const MODULE_PATH = MODULE . 'Category/';

    /**
     * enable route and views registering, called from a static context (no $this)
     * @param Container $container
     * @param Router $router
     * @param Renderer $renderer
     * @return void
     */
    public static function register(Container $container, Router $router, Renderer $renderer): void
    {
        $container->set(CategoryDAO::class, new CategoryDAO($container));

        $renderer->addNamespace('category', self::MODULE_PATH . 'View');
        // ACTIONS
        $router->get('/category', CategoryController::class . '::listAction', 'category.list');
        $router->get('/category/{category: \d+}[/{image: \d+}]', CategoryController::class . '::showAction', 'category.show');
        // IMAGES
        $router->get('/category/{category: \d+}/image/first[/{nb: \d+}]', CategoryController::class . '::firstImageAction', 'category.image.first');
        $router->get('/category/{category: \d+}/image/random[/{nb: \d+}]', CategoryController::class . '::randomImageAction', 'category.image.random');
        $router->get('/category/{category: \d+}/image/{image: \d+}/jump/{forward: \d{1}}[/{nb: \d+}]', CategoryController::class . '::jumpImageAction', 'category.image.random');
        $router->get('/category/{category: \d+}/image/{image: \d+}', CategoryController::class . '::showImageAction', 'category.image.show');
        $router->get('/category/{category: \d+}/image/{image: \d+}/grid[/{nb: \d+}]', CategoryController::class . '::showImageGridAction', 'category.image.grid');
        // CRUD
        $router->post('/category[/create]', CategoryController::class . '::createAction', 'category.create');
        $router->post('/category/update/{id: \d+}', CategoryController::class . '::updateAction', 'category.update');
        $router->post('/category/delete/{id: \d+}', CategoryController::class . '::deleteAction', 'category.delete');
    }

    private $dao;
    private $imageDao;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->dao = $container->get(CategoryDAO::class);
        $this->imageDao = $container->get(DbImageDAO::class);
    }

    public function listAction(Request $request): Response
    {
        $categories = $this->dao->getCategories();

        return new Response(200, [], $this->renderer->render('@category/list', [
            'categories' => $categories->asArray()
        ]));
    }

    public function showAction(Request $request): Response
    {
        $imgId = $request->getParameters()->get('image');
        $category = $this->dao->getCategory((int)$request->getParameters()->get('category'));

        if ($category->getImages()->isEmpty()) {
            return new Response(200, [], $this->renderer->render('@category/empty'));
        }

        // show every images of the category
        if ($imgId === null) {
            return (new Response())->withRedirect($this->router->build('category.image.grid', [
                'category' => $category->getId(),
                'image' => $category->getImages()->first(),
                'nb' => $category->getImages()->length()
            ]));
        }

        // show the current image
        return (new Response())->withRedirect($this->router->build('category.image.single', [
            'category' => $category->getId(),
            'image' => $this->imageDao->getImage($imgId)->getId()
        ]));
    }


    public function firstImageAction(Request $request): Response
    {
        $category = $this->dao->getCategory($request->getParameters()->get('category'));
        $nb = $request->getParameters()->get('nb');

        if ($nb !== null) {
            return (new Response())->withRedirect($this->router->build('category.image.grid', [
                'category' => $category->getId(),
                'image' => $category->getImages()->first(),
                'nb' => $nb
            ]));
        }

        return (new Response())->withRedirect($this->router->build('category.image.show', [
            'category' => $category->getId(),
            'image' => $category->getImages()->first()
        ]));
    }

    public function jumpImageAction(Request $request): Response
    {
        $forward = (int)$request->getParameters()->get('forward');
        $category = $this->dao->getCategory($request->getParameters()->get('category'));
        $img = $this->imageDao->getImage($request->getParameters()->get('image'));
        $nb = $request->getParameters()->get('nb');

        // jump to grid
        if ($nb !== null) {
            return (new Response())->withRedirect($this->router->build('category.image.grid', [
                'category' => $category->getId(),
                'image' => $category->jumpToImage($img, $forward ? $nb : -$nb)->getId(),
                'nb' => $nb
            ]));
        }

        // jump to single
        return (new Response())->withRedirect($this->router->build('category.image.show', [
            'id' => ($forward
                ? $category->jumpToImage($img, 1)
                : $category->jumpToImage($img, -1)
            )->getId(),
        ]));
    }

    public function randomImageAction(Request $request): Response
    {
        $category = $this->dao->getCategory($request->getParameters()->get('category'));
        $nb = $request->getParameters()->get('nb');

        // jump to grid
        if ($nb !== null) {
            return (new Response())->withRedirect($this->router->build('category.image.grid', [
                'category' => $category,
                'image' => $category->getRandomImage()->getId(),
                'nb' => $nb
            ]));
        }

        // jump to single
        return (new Response())->withRedirect($this->router->build('category.image.show', [
            'category' => $category,
            'image' => $category->getRandomImage()->getId(),
        ]));
    }


    public function showImageAction(Request $request): Response
    {
        // show the image for the current category
        $category = $this->dao->getCategory($request->getParameters()->get('category'));
        $img = $this->imageDao->getImage(
            $request->getParameters()->get('image', $this->imageDao->getFirstImage()->getId())
        );

        return new Response(200, [], $this->renderer->render('@category/image/show', [
            'category' => $category,
            'image' => $img
        ]));
    }

    public function showImageGridAction(Request $request): Response
    {
        // show $nb images of the category
        $category = $this->dao->getCategory($request->getParameters()->get('category'));
        $img = $this->imageDao->getImage(
            $request->getParameters()->get('image', $this->imageDao->getFirstImage()->getId())
        );
        $nb = (int)$request->getParameters()->get('nb', 2);

        return new Response(200, [], $this->renderer->render('@category/image/show', [
            'category' => $category,
            'images' => $category->getImagesList($img, $nb),
            'nb' => $nb,
            'nextNb' => $nb * 2
        ]));
    }

}