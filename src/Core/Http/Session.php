<?php

namespace App\Core\Http;

class Session
{

    /**
     * @var Session|null
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
            self::$instance = new Session();
        }
        return self::$instance;
    }

    /**
     * store a session key
     * @param string $key
     * @param string $value
     * @param int|null $expire
     */
    public function set(string $key, string $value, ?int $expire = null)
    {
        $_SESSION[$key] = $value;
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
        return array_key_exists($key, $_SESSION);
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

    /**
     * destruct a session
     */
    public function destroy(): void
    {
        session_destroy();
    }

    /**
     * reset values of a session
     */
    public function reset(): void
    {
        session_unset();
    }

}