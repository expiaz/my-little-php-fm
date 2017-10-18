<?php

namespace App\Core\Dispatcher;

use App\Core\Exception\ClassNotFoundException;
use App\Core\Renderer;

class FrontController
{

    public const DEFAULT_CONTROLLER = 'Index';
    public const DEFAULT_ACTION = 'index';
    private const ROOT_RESSOURCE = '/';
    private const RESSOURCE_DELIMITER = '/';
    private const URL_SCHEME = '://';

    /**
     * parse the string then load the given file
     * @param string $url
     * @return string
     */
    public function dispatch(string $url): string{
        $entry = $this->parse($url);
        return $this->load($entry);
    }

    /**
     * extract the ressource part of an URL e.g http://google.fr/search/a becomes search/a
     * @param string $url
     * @return bool|string
     */
    public function extractRessource(string $url):string
    {
        // pos of :// in 'http://site.com'
        $hasHost = strpos($url, self::URL_SCHEME);

        $uri = "";

        // if there was the host in the url, extract the ressource only
        if($hasHost !== false){
            // 'http://site.com/a' becomes 'site.com/a'
            $urlWoScheme = substr($url, $hasHost + strlen(self::URL_SCHEME));

            // find the position of '/a' after 'site.com' in 'site.com/a' or false if no ressource exists e.g in 'site.com'
            $hostEnd = strpos($urlWoScheme, self::RESSOURCE_DELIMITER);

            // if the url has a ressource e.g was 'site.com/something' and not 'site.com'
            if($hostEnd !== false){
                // only take the ressource 'site.com/a' => 'a'
                $uri = substr($urlWoScheme, $hostEnd + 1);
            }
        } else {
            $uri = $url;
        }

        // no ressource becomes root ressource
        if(strlen($uri) === 0){
            $uri = self::ROOT_RESSOURCE;
        } elseif($uri !== self::ROOT_RESSOURCE){ // trim the '/'
            // '/a/b/ becomes 'a/b'
            $uri = trim($uri, '/');
        }

        return $uri;
    }

    /**
     * cut the string in pieces separated by '/', first piece is controller, second one action, others are arguments
     * e.g get/my/ding/dong give controller => get, action => my, parameters [ding, dong]
     * @param string $url
     * @return Entry
     */
    public function parse(string $url): Entry
    {
        $uri = $this->extractRessource($url);

        if($uri === self::ROOT_RESSOURCE){
            return new Entry(self::DEFAULT_CONTROLLER, self::DEFAULT_ACTION, []);
        }

        // split it in pieces separated with '/'
        $pieces = explode('/', $uri);

        $controller = $pieces[0] ?? self::DEFAULT_CONTROLLER;
        $action = $pieces[1] ?? self::DEFAULT_ACTION;
        $parameters = array_slice($pieces, 2);

        return new Entry($controller, $action, $parameters);
    }

    /**
     * try to load the file described by the entry and call it with the given method if it exists and parameters
     * @param Entry $entry
     * @return string
     * @throws ClassNotFoundException
     */
    public function load(Entry $entry): string
    {
        //here come the Inversion Of Control, we'll load the controller and call the action with parameters on it
        //first let's ensure that it exists
        $controllerPath = SRC . 'Controller' . DS;
        $controllerNameFormat = '%sController';
        $actionNameFormat = '%sAction';

        // 'nAmE' => 'Name'
        $controllerName = ucfirst(strtolower($entry->controller));
        // %sController => NameController
        $controllerName = sprintf($controllerNameFormat, $controllerName);

        //get back to default if there's no controller file
        if (! file_exists($controllerPath . $controllerName . '.php')) {
            $controllerName = sprintf($controllerNameFormat, self::DEFAULT_CONTROLLER);
        }

        $controllerFile = $controllerPath . $controllerName . '.php';

        require_once $controllerFile;

        $controllerNamespace = "App\\Controller\\";
        $controllerClassName = $controllerNamespace . $controllerName;

        if(! class_exists($controllerClassName)){
            throw new ClassNotFoundException("$controllerClassName does not exists in $controllerFile", $controllerClassName);
        }

        $controllerInstance = new $controllerClassName(new Renderer()/* arguments for controller */);

        $actionName = $entry->action;
        $actionName = sprintf($actionNameFormat, $actionName);

        //let's now ensure that the method exists
        if (! method_exists($controllerInstance, $actionName)) {
            $actionName = sprintf($actionNameFormat, self::DEFAULT_ACTION);
        }

        return $controllerInstance->{$actionName}($entry->parameters);

        //call_user_func_array([$controllerInstance, $action], $this->parameters);
        //OR $contollerInstance->{$action}(... $this->parameters);
    }
}