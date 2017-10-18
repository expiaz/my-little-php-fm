<?php

namespace App\Core\Http;

use InvalidArgumentException;

class Uri
{
    private $defaults;

    private $scheme;
    private $host;
    private $port;
    private $path;
    private $query;
    private $fragment;

    public static function fromGlobals(): self
    {
        return new self($_SERVER['REQUEST_URI']);
    }

    public function __construct(string $url)
    {
        // if begin with the path, concat the host
        if($url[0] === '/'){
            $url = WEBHOST . $url;
        }

        // force to recognize and separate host and path in parse_url function
        if(strpos($url, '://') === false){
            $url = WEBSCHEME . '://' . $url;
        }

        $this->defaults = [
            'scheme' => WEBSCHEME,
            'host' => WEBHOST,
            'port' => '80',
            'path' => '/',
            'query' => '',
            'fragment' => ''
        ];

        $parts = $this->extractParts($url);
        $this->scheme = $parts['scheme'];
        $this->host = $parts['host'];
        $this->port = $parts['port'];
        $this->path = $parts['path'];
        $this->query = $parts['query'];
        $this->fragment = $parts['fragment'];
    }

    private function extractParts(string $url): array
    {
        $parts = parse_url($url);

        if($parts === false){
            throw new InvalidArgumentException("[Uri::extractParts] Unable to parse $url");
        }

        return array_merge(
            $this->defaults,
            $parts
        );
    }

    /**
     * @return mixed
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @return mixed
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return mixed
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    public function getFullUrl(): string
    {
        $full = $this->scheme . '://';
        $full .= $this->host;
        if( (int) $this->port !== 80){
            $full .= ':' . $this->port;
        }
        $full .= $this->path;
        if(! empty($this->query)){
            $full .= '?' . $this->query;
        }
        if(! empty($this->fragment)){
            $full .= '#' . $this->fragment;
        }

        return $full;
    }

    public function __toString(): string
    {
        return $this->getFullUrl();
    }

}