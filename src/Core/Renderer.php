<?php

namespace App\Core;

use InvalidArgumentException;

class Renderer{

    private const DEFAUT_BP = SRC . 'View';
    private const DEFAUT_NS = '__DEFAULT';

    /**
     * @var array
     */
    private $namespaces = [];

    /**
     * @var array
     */
    private $globals = [];

    /**
     * Renderer constructor.
     * @param null|string $basePath
     */
    public function __construct(?string $basePath = self::DEFAUT_BP)
    {
        $this->namespaces[self::DEFAUT_NS] = $basePath;
    }

    /**
     * @param string $namespace
     * @param string $path
     */
    public function addNamespace(string $namespace, string $path): void
    {
        $this->namespaces[$namespace] = $path;
    }

    /**
     * @param string $name
     * @param $value
     */
    public function addGlobal(string $name, $value){
        $this->globals[$name] = $value;
    }

    /**
     * @param string $string
     * @param int|null $start
     * @param int|null $end
     * @return string
     */
    private function substring(string $string, ?int $start = 0, ?int $end = null): string
    {
        if($end === null){
            $end = strlen($string);
        }

        $length = $end - $start;

        return substr($string, $start, $length);
    }

    /**
     * @param string $path
     * @return string
     */
    private function resolvePath(string $path): string
    {
        if(strpos($path, '/') === false){
            $path = '/' . $path;
        }

        if($path[0] === '@'){
            $ns = $this->substring($path, 1, strpos($path, '/'));
            if(! isset($this->namespaces[$ns])){
                throw new InvalidArgumentException("$ns is not a valid namespace for a view");
            }
            $view = str_replace('@' . $ns, $this->namespaces[$ns], $path);
        } else {
            $view = $this->namespaces[self::DEFAUT_NS] . $path;
        }

        $file = $view . '.php';

        if(! file_exists($file)){
            throw new InvalidArgumentException("$file is not a valid file for a view");
        }

        return $file;
    }

    /**
     * @param string $view
     * @param array|null $variables
     * @return string
     * @throws \Exception
     */
    public function render(string $view, ?array $variables = []): string
    {
        $startLevel = ob_get_level();
        ob_start();

        extract($this->globals);

        extract($variables, EXTR_SKIP);

        require_once($this->resolvePath($view));

        $content = ob_get_clean();

        $endLevel = ob_get_level();

        if($startLevel !== $endLevel){
            throw new \Exception("[View::render] ob_level fails expected $startLevel got $endLevel");
        }

        return $content;
    }



}