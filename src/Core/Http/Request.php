<?php

namespace App\Core\Http;

use App\Core\Http\Router\Match;
use App\Core\Utils\ParameterBag;
use App\Core\Utils\UploadedFile;

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
    private $files;
    private $parameters;

    /**
     * create the request from the global variables $_SERVER, $_GET, $_POST
     * @return Request
     */
    public static function fromGlobals(): self
    {
        return new self(
            WEBMETHOD,
            WEBURL,
            $_GET,
            $_POST,
            $_FILES
        );
    }

    public static function fromMatch(Match $match): self
    {
        return new self(
            WEBMETHOD,
            WEBURL,
            $_GET,
            $_POST,
            $_FILES,
            $match->getParameters()->asArray()
        );
    }

    /**
     * Request constructor.
     * @param string $method
     * @param string $url
     * @param array|null $query
     * @param array|null $request
     * @param array|null $files
     * @param array|null $parameters
     */
    public function __construct(
        string $method,
        string $url,
        ?array $query = [],
        ?array $request = [],
        ?array $files = [],
        ?array $parameters = []
    )
    {
        $this->uri = new Uri($url);
        $this->method = $this->guessMethod($method);
        $this->query = new ParameterBag($query);
        $this->request = new ParameterBag($request);
        $this->files = UploadedFile::fromArray($files);
        $this->parameters = new ParameterBag($parameters);
    }

    /**
     * @param string $method
     * @return string
     */
    private function guessMethod(string $method): string
    {
        $method = strtoupper($method);

        if(! in_array($method, [self::GET, self::POST, self::PUT, self::DELETE])){
            throw new \InvalidArgumentException("[Request::construct] $method is not a valid method");
        }

        return $method;
    }

    /**
     * @return Uri
     */
    public function getUri(): Uri
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * equivalent of $_GET
     * @return ParameterBag
     */
    public function getQuery(): ParameterBag
    {
        return $this->query;
    }

    /**
     * equivalent of $_POST
     * @return ParameterBag
     */
    public function getParsedBody(): ParameterBag
    {
        return $this->request;
    }

    /**
     * @return ParameterBag
     */
    public function getUploadedFiles(): ParameterBag
    {
        return $this->files;
    }

    /**
     * @return ParameterBag
     */
    public function getParameters(): ParameterBag
    {
        return $this->parameters;
    }

}