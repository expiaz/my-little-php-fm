<?php

namespace App\Core\Http;

class Request
{

    public const GET = 'GET';
    public const PUT = 'PUT';
    public const DELETE = 'DELETE';
    public const POST = 'POST';

    private $uri;
    private $method;
    private $query;
    private $request;

    /**
     * create the request from the global variables $_SERVER, $_GET, $_POST
     * @return Request
     */
    public static function fromGlobals(): self
    {
        return new self(
            $_SERVER['REQUEST_METHOD'] ?? '',
            WEBURL,
            $_GET,
            $_POST
        );
    }

    /**
     * Request constructor.
     * @param string $method
     * @param string $url
     * @param array|null $query
     * @param array|null $request
     */
    public function __construct(string $method, string $url, ?array $query = [], ?array $request = [])
    {
        $this->uri = new Uri($url);
        $this->method = $this->guessMethod($method);
        $this->query = new ParameterBag($query);
        $this->request = new ParameterBag($request);
    }

    private function guessMethod(string $method): string
    {
        $method = strtoupper($method);

        if(! in_array($method, [self::GET, self::POST, self::PUT, self::DELETE])){
            throw new \InvalidArgumentException("[Request::construct] $method is not a valid method");
        }

        return $method;
    }

    public function getUri(): Uri
    {
        return $this->uri;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getQuery(): ParameterBag
    {
        return $this->query;
    }

    public function getParsedBody(): ParameterBag
    {
        return $this->request;
    }

}