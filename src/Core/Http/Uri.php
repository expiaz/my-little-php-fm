<?php

namespace App\Core\Http;

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
        $this->defaults = array_merge(
            [
                'scheme' => 'http',
                'host' => '',
                'port' => '80',
                'path' => '',
                'query' => '',
                'fragment' => ''
            ],
            parse_url(WEBURL)
        );

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

        $parts['scheme'] = $parts['scheme'] ?? $this->defaults['scheme'];
        $parts['host'] = $parts['host'] ?? $this->defaults['host'];

        $parts['port'] = $parts['port'] ?? $this->defaults['port'];
        $parts['path'] = $parts['path'] ?? $this->defaults['path'];
        $parts['query'] = $parts['query'] ?? $this->defaults['query'];
        $parts['fragment'] = $parts['fragment'] ?? $this->defaults['fragment'];

        return $parts;
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

}