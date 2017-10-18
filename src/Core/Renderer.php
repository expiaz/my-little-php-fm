<?php

namespace App\Core;

class Renderer{

    public function render(string $view, ?array $variables = []){
        try{
            return (new View($view))->render($variables);
        } catch (\InvalidArgumentException $e){
            return $e->getMessage();
        } catch (\Exception $e){
            return $e->getMessage();
        }
    }

}