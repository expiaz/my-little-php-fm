<?php

namespace App\Module\User\Controller;

use App\Core\BaseController;
use App\Core\Container;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Router\Router;
use App\Core\Http\Session;
use App\Core\Renderer;
use App\Module\User\Model\Repository\UserDAO;

class UserController extends BaseController
{

    public const MODULE_PATH = MODULE . 'User/';

    /**
     * @var UserDAO
     */
    private $dao;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->dao = $container->get(UserDAO::class);
    }

    /**
     * enable route and views registering, called from a static context (no $this)
     * @param Container $container
     * @param Router $router
     * @param Renderer $renderer
     * @return void
     */
    public static function register(Container $container, Router $router, Renderer $renderer): void
    {
        $renderer->addNamespace('user', self::MODULE_PATH . 'View');
        $router->get('/auth', UserController::class . '::auth', 'user.auth');
        $router->post('/auth', UserController::class . '::auth', 'user.auth.post');
        $router->get('/deco', UserController::class . '::deconnection', 'user.deco');
    }

    public function auth(Request $request): Response
    {
        // already connected
        $session = Session::getInstance();
        if ($session->has('user')) {
            return (new Response())->withRedirect($this->router->build('site.home'));
        }

        if (
            $request->getParsedBody()->get('login') !== null
            && $request->getParsedBody()->get('password') !== null
        ) {
            // form submitted
            $user = $this->dao->auth(
                $request->getParsedBody()->get('login'),
                $request->getParsedBody()->get('password')
            );
            if($user === null){
                // bad credentials
                return new Response(200, [],$this->renderer->render('@user/auth', [
                    'error' => true,
                    'message' => 'Bad credentials'
                ]));
            }

            $session->set('user', $user->getId());

            return (new Response())->withRedirect($this->router->build('site.home'));
        }

        // first time coming on the page
        return new Response(200, [], $this->renderer->render('@user/auth', [
            'error' => false
        ]));
    }

    public function deconnection(Request $request): Response
    {
        Session::getInstance()->delete('user');
        return (new Response())->withRedirect($this->router->build('site.home'));
    }

}