<?php

namespace App\Core;

class View
{
    private $template;

    public function __construct(string $template)
    {
        $path = $this->makePath($template);
        if(! file_exists($path)){
            throw new \InvalidArgumentException("[View::construct] $template does not exists in $path");
        }

        $this->template = $path;
    }

    private function makePath(string $path): string
    {
        if($path[0] !== DS){
            $path = DS . $path;
        }

        if(substr($path, strlen($path) - 4, 4) !== '.php'){
            $path .= '.php';
        }

        return SRC . 'View' . $path;
    }

    public function render(?array $variables = []): string
    {
        $startLevel = ob_get_level();
        ob_start();

        extract($variables, EXTR_SKIP);

        require_once SRC . 'View' . DS . 'layout' . DS . 'header.php';

        require_once($this->template);

        require_once SRC . 'View' . DS . 'layout' . DS . 'footer.php';

        $content = ob_get_clean();

        $endLevel = ob_get_level();

        if($startLevel !== $endLevel){
            throw new \Exception("[View::render] ob_level fails expected $startLevel got $endLevel");
        }

        return $content;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

}