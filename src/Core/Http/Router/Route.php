<?php

namespace App\Core\Http\Router;

use App\Core\Http\Uri;
use App\Core\Utils\Stack;

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

    private const VARIABLE_NAME_REGEX = '([a-zA-Z_][a-zA-Z0-9_-]*)';
    private const VARIABLE_VALUE_REGEX = '([^{}]*(?:\{(?-1)\}[^{}]*)*)';

    public const DEFAULT_DISPATCH_REGEX = '[^/]+';

    /**
     * @var string the name of the route used to re-build it
     */
    private $name;

    /**
     * @var string the path as given by the user
     */
    private $path;

    /**
     * @var string the reg-exp matching the path's pattern
     */
    private $regex;

    /**
     * @var string the path with the placeholders like '{name: filter}'
     * replaced by '{{name}}' for more easy re-building
     */
    private $template;

    /**
     * @var string the function or class that'll be called when the route will be matched
     */
    private $handler;

    /**
     * @var array the parameters of the route (placeholders) and their filters
     */
    private $parameters = [];

    /**
     * @var Stack<string> the middlewares to call before this route's handler
     */
    private $middlewares;

    /**
     * Route constructor.
     * @param string $name
     * @param string $path
     * @param string $handler
     */
    public function __construct(string $name, string $path, string $handler)
    {
        $this->name = $name;
        $this->path = $path;
        $this->handler = $handler;
        $this->middlewares = new Stack();

        $this->parsePath($path);
    }

    /**
     * escape reserved caracters from a string and format it in a regex for PCRE
     * @param string $str
     * @return string
     */
    private function regexify(string $str): string
    {
        return '~^' . str_replace('/','\/',$str) . '$~';
    }

    /**
     * parse the placeholders of a route
     * @param string $path
     */
    private function parsePath(string $path): void
    {
        //no placeholders
        if (! preg_match_all(
            '~' . self::VARIABLE_REGEX . '~x', $path, $matches,
            PREG_OFFSET_CAPTURE | PREG_SET_ORDER
        )) {
            $this->regex = $this->regexify($path);
            $this->template = $path;
            return;
        }

        $stub = $path;
        $parameters = [];

        /*
         * $set = [
         *      [
         *          full match e.g '{name: regex}',
         *          index
         *      ]
         *      [
         *          name,
         *          index,
         *      ]
         *      [
         *          regex,
         *          index
         *      ]
         *  ]
         */
        foreach ($matches as $set) {
            // example done with '/test/{name: regex}/smth'
            $fullMatch = $set[0][0];

            // 'regex'
            $filter = isset($set[2])
                ? trim($set[2][0])
                : self::DEFAULT_DISPATCH_REGEX;
            // 'name'
            $parameterName = $set[1][0];

            // '/test/'
            $beforeMatch = substr($stub, 0, strpos($stub, $fullMatch));
            // '/smth'
            $afterMatch = substr($stub,strpos($stub, $fullMatch) + strlen($fullMatch));

            // '/test/(regex)/smth'
            $stub = $beforeMatch . '{{' . $parameterName . ':' . $filter . '}}' . $afterMatch;

            // 'name'
            $parameters[$parameterName] = $filter;
        }

        // replace '{{name:filter}}' by '(filter)'
        $regex = preg_replace_callback(
            '~{{' . self::VARIABLE_NAME_REGEX . ':' . self::VARIABLE_VALUE_REGEX . '}}~',
            function(array $matches): string{
                return '(' . $matches[2] . ')';
            },
            $stub
        );

        // replace '{{name:filter}}' by '{{name}}'
        $template = preg_replace_callback(
            '~{{' . self::VARIABLE_NAME_REGEX . ':' . self::VARIABLE_VALUE_REGEX . '}}~',
            function(array $matches): string{
                return '{{' . $matches[1] . '}}';
            },
            $stub
        );

        $this->template = $template;
        $this->regex = $this->regexify($regex);
        $this->parameters = $parameters;
    }

    /**
     * replace the placeholders of a route with the given values
     * @param array|null $parameters
     * @return Uri|null if $parameters aren't correct
     * @throws \Exception
     */
    public function buildUri(?array $parameters = [])
    {
        //ensure that we get the needed parameters
        foreach ($this->parameters as $name => $filter){
            $regex = '~^' . $filter . '$~';
            if(
                ! array_key_exists($name, $parameters)
                || ! preg_match($regex, $parameters[$name])
            ){
                return null;
            }
        }

        // replace every regex group by it's value provided in $parameters
        $rebuiltPath = preg_replace_callback(
            '~\{\{' . self::VARIABLE_NAME_REGEX . '\}\}~',
            function(array $matches) use ($parameters): string {
                return $parameters[$matches[1]];
            },
            $this->template
        );

        return new Uri($rebuiltPath);
    }


    /**
     * rename the route
     * @param string $name
     * @return Route
     */
    public function as(string $name): Route
    {
        $this->name = $name;
        return $this;
    }

    /**
     * add a middleware to the queue
     * @param string $middleware
     * @return Route
     */
    public function use(string $middleware): Route
    {
        $this->middlewares->push($middleware);
        return $this;
    }


    /**
     * @return string
     */
    public function getName(): string
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
    public function getRegex(): string
    {
        return $this->regex;
    }

    /**
     * @return string
     */
    public function getHandler(): string
    {
        return $this->handler;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return Stack<string>
     */
    public function getMiddlewares(): Stack
    {
        return $this->middlewares;
    }
}