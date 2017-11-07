<?php

namespace App\Core\Http\Router;

use App\Core\Dispatcher;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Utils\Resolver;
use Exception;

class Middleware
{

    /**
     * @var Middleware|null
     */
    private $next;
    /**
     * @var callable
     */
    private $handler;
    /**
     * @var Dispatcher
     */
    private $resolver;

    /**
     * Middleware constructor.
     * @param Resolver $resolver
     * @param string $handler
     * @param Middleware|null $next
     */
    public function __construct(Resolver $resolver, string $handler, ?Middleware $next)
    {
        $this->next = $next;
        $this->resolver = $resolver;
        $this->wrap($handler);
    }

    /**
     * parse the handler and wrap it into a closure for future use
     * @param $handler
     * @throws Exception
     */
    private function wrap($handler)
    {
        $handlerPieces = $this->resolver->resolveHandler($handler);

        if(count($handlerPieces) !== 2){
            throw new Exception("Middleware::wrap, can't resolve $this->handler, not valid handler");
        }

        /**
         * @param Request $request
         * @return Response|Request
         */
        $this->handler = function (Request $request) use($handlerPieces) {
            return $this->resolver->resolve($handlerPieces[0])->{$handlerPieces[1]}($request);
        };
    }

    public function call(Request $request): Response
    {
        $rep = call_user_func($this->handler, $request);
        if($rep instanceof Response){
            // fin de la chaîne
            return $rep;
        } else if($rep instanceof Request){
            // continuité
            if($this->next !== null){
                return $this->next->call($rep);
            }
            throw new Exception("Middleware::call chain break, no next middleware for request");
        }
        throw new Exception("Middleware::call $rep is not a valid return type for a middleware");
    }

}