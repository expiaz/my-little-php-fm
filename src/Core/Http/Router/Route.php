<?php

namespace App\Core\Http\Router;

use App\Core\Http\Uri;
use Exception;

/**
 * Represents a path with or without arguments
 * Class Route
 * @package App\Core\Http\Router
 */
final class Route
{

    public const VARIABLE_REGEX = <<<'REGEX'
\{
    \s* ([a-zA-Z_][a-zA-Z0-9_-]*) \s*
    (?:
        : \s* ([^{}]*(?:\{(?-1)\}[^{}]*)*)
    )?
\}
REGEX;
    public const DEFAULT_DISPATCH_REGEX = '[^/]+';

    /**
     * @var string
     * the name of the route (e.g 'route.name')
     */
    private $name;

    /**
     * @var string
     * the path given by the user
     */
    private $path;

    /**
     * @var Path the normal Path to match for this route
     * e.g in '/test[/opt]' this is '/test'
     */
    private $normalPath;

    /**
     * @var Path[] the optionnals Path for this route
     * e.g in '/test[/opt]' this is '/test/opt'
     */
    private $optionnalPaths;

    /**
     * @var string
     * the handler for the route (generally a controller)
     */
    private $handler;

    /**
     * @var Middleware[]
     * the middlewares for this route
     */
    private $middlewares;

    /**
     * Route constructor.
     * @param string $name
     * @param string $path the path wanted to be matched
     * @param string $handler
     */
    public function __construct(string $name, string $path, string $handler)
    {
        $this->name = $name;
        $this->path = $path;
        $this->handler = $handler;
        $this->middlewares = [];

        $paths = $this->parseOptionnals($path);
        $this->normalPath = new Path($paths[0]);
        $this->optionnalPaths = array_map(
            function (string $path) {
                return new Path($path);
            },
            array_slice($paths, 1)
        );
    }

    /**
     * preprocess a path to determine possibles routes from optionnals
     * e.g '/test[/opt]' becomes '/test' and '/test/opt'
     * @param string $path
     * @return array
     * @throws Exception
     */
    private function parseOptionnals(string $path): array
    {
        $routeWithoutClosingOptionals = rtrim($path, ']');
        $numOptionals = strlen($path) - strlen($routeWithoutClosingOptionals);

        // Split on [ while skipping placeholders
        $segments = preg_split('~' . Route::VARIABLE_REGEX . '(*SKIP)(*F) | \[~x', $routeWithoutClosingOptionals);
        if ($numOptionals !== count($segments) - 1) {
            // If there are any ] in the middle of the route, throw a more specific error message
            if (preg_match('~' . Route::VARIABLE_REGEX . '(*SKIP)(*F) | \]~x', $routeWithoutClosingOptionals)) {
                throw new Exception("[Router::preprocessOptionnals] Optional segments can only occur at the end of a route");
            }
            throw new Exception("[Router::preprocessOptionnals] Number of opening '[' and closing ']' does not match");
        }

        /*
         * for '/test[/opt]
         * segment = [
         *      '/test'
         *      '/opt'
         * ]
         */
        $currentRoute = '';
        $routesPossibles = [];
        foreach ($segments as $n => $segment) {
            if ($segment === '' && $n !== 0) {
                throw new Exception("[Router::preprocessOptionnals] Empty optional part");
            }
            $currentRoute .= $segment;
            $routesPossibles[] = $currentRoute;
        }

        // ['/test', '/test/opt']
        return $routesPossibles;
    }

    /**
     * @param Uri $uri
     * @return Match|null
     */
    public function match(Uri $uri)
    {
        foreach ($this->getAllPaths() as $path) {
            if (preg_match(
                $path->getRegex(),
                $uri->getPath(),
                $parameters
            )) {
                $match = new Match($this, $path);
                $arguments = $path->getParameters();
                $nb = 0;
                // $parameters starts at index 1 because index 0 is the full match
                foreach ($arguments as $argument) {
                    $match->addParameter($argument, $parameters[++$nb]);
                }

                return $match;
            }
        }

        return null;
    }

    /**
     * @param array|null $parameters
     * @return Uri|null
     */
    public function build(?array $parameters = [])
    {
        foreach ($this->getAllPaths() as $path){
            $rebuiltPath = $path->buildPath($parameters);
            if($rebuiltPath !== null){
                return $rebuiltPath;
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @return Path
     */
    public function getNormalPath(): Path
    {
        return $this->normalPath;
    }

    /**
     * @return Path[]
     */
    public function getOptionnalPaths(): array
    {
        return $this->optionnalPaths;
    }

    /**
     * @return Path[]
     */
    public function getAllPaths(): array
    {
        return array_merge([$this->normalPath], $this->optionnalPaths);
    }

}