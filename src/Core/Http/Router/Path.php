<?php

namespace App\Core\Http\Router;

use App\Core\Http\Uri;

class Path
{
    /**
     * @var string the path given by the user
     */
    private $path;

    /**
     * @var string the filter to match the path
     */
    private $regex;

    /**
     * @var string[]
     * the placeholders extracted from the path and their filters [$parameter => $filter]
     */
    private $parameters;

    public function __construct(string $path)
    {
        $this->path = $path;
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
            '~' . Route::VARIABLE_REGEX . '~x', $path, $matches,
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
                : Route::DEFAULT_DISPATCH_REGEX;
            // '/smth'
            $afterMatch = substr($regex,strpos($regex, $set[0][0]) + strlen($set[0][0]));
            // '/test/(regex)/smth'
            $regex = $beforeMatch . '(' . $match . ')' . $afterMatch;
            // 'name'
            $parameters[$set[1][0]] = $set[1][1];
        }

        $this->regex = $this->regexify($path);
        $this->parameters = $parameters;
    }

    /**
     * replace the placeholders of a route with the given values
     * @param array|null $parameters
     * @return Uri|null
     */
    public function buildPath(?array $parameters = [])
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
            '~\([^(]+\)~',
            function(array $matches) use ($parameters): string {
                return $parameters[$matches[1]];
            },
            $this->regex
        );

        return $rebuiltPath;
    }


    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return mixed
     */
    public function getRegex(): string
    {
        return $this->regex;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

}