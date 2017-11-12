<?php

namespace App\Core;

use App\Core\Utils\Context;
use App\Core\Utils\ParameterBag;
use App\Core\Utils\Stack;
use App\Module\Site\Controller\SiteController;
use Exception;
use InvalidArgumentException;

class Renderer{

    private const DEFAUT_BP = SiteController::MODULE_PATH . 'View';
    private const DEFAUT_NS = '__DEFAULT';

    /**
     * @var array
     */
    private $namespaces = [];

    /**
     * @var Context the global variables which will be added to every context
     */
    private $globals;

    /**
     * @var Stack of Context
     * a stack to represent context nesting of rendered views (for inclusions)
     */
    private $contexts;

    /**
     * Renderer constructor.
     * @param null|string $basePath
     */
    public function __construct(?string $basePath = self::DEFAUT_BP)
    {
        $this->contexts = new Stack([new Context()]);

        $this->globals = new Context();

        $this->namespaces[self::DEFAUT_NS] = $basePath;
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
        // 'view' => '/view' to resolve the file path
        if(strpos($path, '/') === false){
            $path = '/' . $path;
        }

        // namespaced like '@ns/view'
        if($path[0] === '@'){
            // 'ns'
            $ns = $this->substring($path, 1, strpos($path, '/'));
            if(! isset($this->namespaces[$ns])){
                throw new InvalidArgumentException("$ns is not a valid namespace for a view");
            }
            // '@ns/view' => 'filepath/view'
            $view = str_replace("@$ns", $this->namespaces[$ns], $path);
        } else {
            // '/view' => 'defaultpath/view'
            $view = $this->namespaces[self::DEFAUT_NS] . $path;
        }

        // 'filepath/view' => filepath/view.php'
        $file = "$view.php";

        if(! file_exists($file)){
            throw new InvalidArgumentException("$file is not a valid file for a view");
        }

        return $file;
    }

    /**
     * @return Context
     */
    public function getCurrentContext(): Context
    {
        return $this->contexts->peek();
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
        $this->globals->set($name, $value);
    }

    /**
     * @param string $view name of the view + ns
     * @param array|null $context variables for the view
     * @param bool|null $isolate does the current context don't have to be merged with parent's one
     * @return string the rendered view
     * @throws Exception
     */
    public function render(string $view, ?array $context = [], ?bool $isolate = false): string
    {
        $_path = $this->resolvePath($view);

        if($isolate){
            $context = new Context($context);
        } else {
            // merge it with parent's context, permissive to redefine variables with array_merge
            $context = new Context(
                array_merge(
                    $this->contexts->peek()->asArray(),
                    $context
                )
            );
        }

        // delete these variables from table symbol, to prevent it from interfering with view variables
        unset($view, $isolate);

        // push the view's context
        $this->contexts->push($context);

        // to ensure that every output buffer opened in the view is closed
        $_startObLevel = ob_get_level();

        // start recording

        ob_start();

        extract($this->globals->asArray(), EXTR_SKIP);

        extract($context->asArray());

        require_once($_path);

        $content = ob_get_clean();

        // end recording

        $endLevel = ob_get_level();

        if($_startObLevel !== $endLevel){
            throw new Exception("[View::render] ob_level fails expected $_startObLevel got $endLevel");
        }

        // delete this view's context
        $this->contexts->pop();

        return $content;
    }



}