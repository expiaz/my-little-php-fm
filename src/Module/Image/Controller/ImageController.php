<?php

namespace App\Module\Image\Controller;

use App\Core\BaseController;
use App\Core\Container;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Router\Router;
use App\Core\Http\Session;
use App\Core\Http\Uri;
use App\Core\Renderer;
use App\Core\Utils\UploadedFile;
use App\Module\Category\Model\Entity\Category;
use App\Module\Category\Model\Repository\CategoryDAO;
use App\Module\Image\Model\Entity\Image;
use App\Module\Image\Model\Repository\DbImageDAO;
use App\Module\Site\Controller\SiteController;
use App\Module\User\Controller\UserController;
use App\Module\User\Model\Entity\User;
use App\Module\User\Model\Repository\UserDAO;
use Exception;

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

        $router->get('/image/register', ImageController::class . '::registerImages', 'image.register');

        $router->get('/image/first[/{nb: \d+}]', ImageController::class . '::firstAction', 'image.first');
        $router->get('/image[/{image: \d+}]', ImageController::class . '::showAction', 'image.show');
        $router->get('/image/grid[/{image: \d+}[/{nb: \d+}]]', ImageController::class . '::gridAction', 'image.grid');
        $router->get('/image/jump/{forward: \d{1}}/{image: \d+}[/{nb: \d+}]', ImageController::class . '::jumpAction', 'image.jump');
        $router->get('/image/random[/{nb: \d+}]', ImageController::class . '::randomAction', 'image.random');

        $router->get('/image/add[/{image: \d+}]', ImageController::class . '::addAction', 'image.add')
            ->use(UserController::class . '::isAuth');
        $router->post('/image/add', ImageController::class . '::addAction', 'image.add')
            ->use(UserController::class . '::isAuth');
    }

    /**
     * @var DbImageDAO
     */
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

    /**
     * @param Request $request
     * @return Response
     */
    public function addAction(Request $request): Response {

        if($request->getMethod() === Request::POST) {

            $image = $request->getUploadedFiles()->get('image');

            if($image !== null) { // image upload
                /**
                 * @var $image UploadedFile
                 */
                if(! $image->match(UploadedFile::IMAGE)){
                    // error type
                    return new Response(200, [], $this->renderer->render('@image/add', [
                        'error' => true,
                        'message' => 'Format d\'image non valide, seuls jpg/png autorisés',
                        'categories' => $this->container->get(CategoryDAO::class)->getCategories()->asArray()
                    ]));
                }

                $imageName = '/upload' . $image->move($this->container->get('config')->get('image.upload'));
                $imageUrl = $this->container->get('config')->get('image.url') . '/' . $imageName;
            } else if($request->getParsedBody()->get('image') !== null) { // url upload
                try {
                    $imageName = (new Uri($request->getParsedBody()->get('image')))->getFullUrl();
                    $imageUrl = $imageName;
                } catch (Exception $e) {
                    return new Response(200, [], $this->renderer->render('@image/add', [
                        'error' => true,
                        'message' => 'Format d\'URL non valide',
                        'categories' => $this->container->get(CategoryDAO::class)->getCategories()->asArray()
                    ]));
                }
            } else {
                // none => error
                return new Response(200, [], $this->renderer->render('@image/add', [
                    'error' => true,
                    'message' => 'Image non spécifiée',
                    'categories' => $this->container->get(CategoryDAO::class)->getCategories()->asArray()
                ]));
            }

            $user = $this->container->get(UserDAO::class)->getUser($request->getParameters()->get('user'));

            $category = (int) $request->getParsedBody()->get('category');
            if($category === 0) {
                $category = (string) $request->getParsedBody()->get('new-category');

                if(! strlen($category)) {
                    return new Response(200, [], $this->renderer->render('@image/add', [
                        'error' => true,
                        'message' => 'La nouvelle catégorie spécifiée doit avoir un nom',
                        'categories' => $this->container->get(CategoryDAO::class)->getCategories()->asArray()
                    ]));
                }

                $category = $this->container->get(CategoryDAO::class)->addCategory($category);
            } else {
                $category = $this->container->get(CategoryDAO::class)->getCategory($category);
            }

            /**
             * @var $category Category
             */
            /**
             * @var $image Image
             */
            /**
             * @var $user User
             */

            $image = $this->dao->addImage(
                $imageUrl,
                $imageName,
                $category,
                $request->getParsedBody()->get('description', ''),
                $user
            );

            return (new Response())->withRedirect($this->router->build('category.image.show', [
                'category' => $category->getId(),
                'image' => $image->getId()
            ]));
        }

        return new Response(200, [], $this->renderer->render('@image/add', [
            'categories' => $this->container->get(CategoryDAO::class)->getCategories()->asArray()
        ] + ($request->getParameters()->get('image') ? [
                'image' => $this->dao->getImage($request->getParameters()->get('image'))
            ] : [])
        ));
    }

    public function registerAction(Request $request): Response {
        $this->dao->register();
        return (new Response())->withRedirect($this->router->build('image.grid'));
    }


}