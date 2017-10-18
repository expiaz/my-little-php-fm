<?php

namespace App\Core\Http\Router;

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

    private $name;
    private $path;
    private $regex;
    private $handler;

    private $parameters;
    private $middlewares;
    
    public function __construct(string $name, string $path, string $handler)
    {
        $this->name = $name;
        $this->path = $path;
        $this->handler = $handler;
        $this->parameters = [];
        $this->middlewares = [];
        $this->regex = $this->parsePath($path);
    }

    private function regexify(string $str): string
    {
        return '|^' . str_replace('/','\/',$str) . '$|';
    }

    public function parsePath(string $path): void
    {
        //no placeholders
        if (! preg_match_all(
            '~' . self::VARIABLE_REGEX . '~x', $path, $matches,
            PREG_OFFSET_CAPTURE | PREG_SET_ORDER
        )) {
            $this->regex = $this->regexify($path);
            return;
        }

        $regex = $path;
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
            // '/test/'
            $beforeMatch = substr($regex, 0, strpos($regex, $set[0][0]));
            // 'regex'
            $match = isset($set[2])
                ? trim($set[2][0])
                : self::DEFAULT_DISPATCH_REGEX;
            // '/smth'
            $afterMatch = substr($regex,strpos($regex, $set[0][0]) + strlen($set[0][0]));
            // '/test/(regex)/smth'
            $regex = $beforeMatch . '(' . $match . ')' . $afterMatch;
            // 'name'
            $parameters[] = $set[1][0];
        }

        $this->regex = $this->regexify($path);
        $this->parameters = $parameters;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        parent::setter('name', $name);
        $this->name = $name;
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
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getRegex()
    {
        return $this->regex;
    }

    /**
     * @return string
     */
    public function getHandler()
    {
        return $this->handler;
    }


}