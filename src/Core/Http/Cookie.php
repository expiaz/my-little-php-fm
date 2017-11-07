<?php

namespace App\Core\Http;

class Cookie {

    /**
     * @var Cookie|null
     */
    private static $instance = null;

    private function __construct()
    {
    }

    /**
     * @return Session
     */
    public static function getInstance(): Session
    {
        if (self::$instance === null) {
            session_start();
            self::$instance = new Cookie();
        }
        return self::$instance;
    }

    /**
     * store a cookie key
     * @param string $key
     * @param string $value
     * @param int|null $expire
     */
    public function set(string $key, string $value, ?int $expire = null){
        if($expire === null) {
            $expire = time() + 3600;
        }
        setcookie($key,$value,$expire);
    }

    /**
     * retrieve a session key
     * @param string $key
     * @param null $default
     * @return null
     */
    public function get(string $key, $default = null)
    {
        if ($this->has($key)) {
            return $_SESSION[$key];
        }
        return $default;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key)
    {
        return array_key_exists($_SESSION, $key);
    }

    /**
     * delete a session key
     * @param string $key
     */
    public function delete(string $key)
    {
        if ($this->has($key)) {
            unset($_SESSION[$key]);
        }
    }

}