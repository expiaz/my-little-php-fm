<?php

namespace App\Core\Http;

use Exception;
use InvalidArgumentException;
use RuntimeException;

/**
 * respresents an http response
 * Class Response
 * @package App\Core\Http
 */
class Response
{
    /**
     * most used headers
     */
    const CONTENT_TYPE = 'Content-Type';
    const LOCATION = 'Location';

    /**
     * @var array
     * reasonPhrases of statusCode
     */
    protected static $messages = [
        //Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        //Successful 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        //Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        //Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        444 => 'Connection Closed Without Response',
        451 => 'Unavailable For Legal Reasons',
        499 => 'Client Closed Request',
        //Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        599 => 'Network Connect Timeout Error',
    ];

    /**
     * @var string the http protocol used
     */
    protected $protocol;

    /**
     * @var int the status code of the request
     */
    protected $status;

    /**
     * @var string reasonPhrase associated to the status code
     */
    protected $reasonPhrase;

    /**
     * @var array
     * the current request headers
     */
    protected $headers;

    /**
     * @var string
     * the current request body
     */
    protected $body;

    /**
     * Response constructor.
     * @param int $status
     * @param array $headers
     * @param string $body
     * @param string $protocol
     */
    public function __construct(?int $status = 200, ?array $headers = [], ?string $body = '', ?string $protocol = 'HTTP/1.1')
    {
        $this->protocol = $protocol;
        $this->status = $this->filterStatus($status);
        $this->headers = array_merge(
            [
                self::CONTENT_TYPE => 'text/html;charset=UTF-8'
            ],
            $headers
        );
        $this->body = $body;
    }

    /**
     * ensure that a status is valid
     * @param int $status
     * @return int $status
     */
    protected function filterStatus(int $status): int
    {
        if ($status < 100 || $status > 599) {
            throw new InvalidArgumentException('Invalid HTTP status code');
        }

        return $status;
    }

    /*
     * GETTERS
     */

    /**
     * @return string
     */
    public function getProtocol(): string
    {
        return $this->protocol;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getStatusPhrase(): string
    {
        return $this->getReasonPhrase();
    }

    /**
     * @return string
     */
    public function getReasonPhrase(): string
    {
        if (isset(static::$messages[$this->status])) {
            return static::$messages[$this->status];
        }
        return '';
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param string $header
     * @return string
     * @throws Exception
     */
    public function getHeader(string $header): string
    {
        if ($this->hasHeader($header)) {
            return $this->headers[$header];
        }
        throw new Exception("Response::getHeader no {$header}");
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }


    /**
     * change the status of the request, and it's associated reasonPhrase
     * @param int $code
     * @param null|string $reasonPhrase
     * @return Response
     */
    public function withStatus(int $code, ?string $reasonPhrase = ''): Response
    {
        $code = $this->filterStatus($code);

        $this->status = $code;
        if ($reasonPhrase === '' && isset(static::$messages[$code])) {
            $reasonPhrase = static::$messages[$code];
        } else {
            throw new InvalidArgumentException('ReasonPhrase must be supplied for this code : ' . $code);
        }

        $this->reasonPhrase = $reasonPhrase;

        return $this;
    }

    /**
     * does this request have this header registered
     * @param $header
     * @return bool
     */
    public function hasHeader($header): bool
    {
        return isset($this->headers[$header]);
    }

    /**
     * add header to the request
     * @param string $header
     * @param string $value
     * @return Response
     */
    public function withHeader(string $header, string $value): Response
    {
        $this->headers[$header] = $value;
        return $this;
    }

    /**
     * write string to the request's body
     * @param string $data
     * @return Response
     */
    public function write(string $data): Response
    {
        $this->body .= $data;
//        $this->contentLength += strlen($data);
        return $this;
    }

    /*
     * SPECIFIQUE HEADER SHORTCUTS
     */

    /**
     * add a location header to the request and 302 redirect code
     * @param Uri $uri
     * @param int|null $status
     * @return Response
     */
    public function withRedirect(Uri $uri, ?int $status = 302): Response
    {
        $this->withHeader(static::LOCATION, $uri->getFullUrl());

        return $this->withStatus($status);
    }

    /**
     * make request's body json encoded and change content type header
     * @param $data
     * @param int|null $status
     * @param int|null $encodingOptions
     * @return Response
     */
    public function withJson($data, ?int $status = null, ?int $encodingOptions = 0): Response
    {
        $this->write($json = json_encode($data, $encodingOptions));

        // Ensure that the json encoding passed successfully
        if ($json === false) {
            throw new RuntimeException(json_last_error_msg(), json_last_error());
        }

        $responseWithJson = $this->withHeader(self::CONTENT_TYPE, 'application/json;charset=utf-8');

        if ($status !== null) {
            return $responseWithJson->withStatus($status);
        }

        return $responseWithJson;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return in_array($this->getStatusCode(), [204, 205, 304]);
    }

    /**
     * @return bool
     */
    public function isInformational(): bool
    {
        return $this->getStatusCode() >= 100 && $this->getStatusCode() < 200;
    }

    /**
     * @return bool
     */
    public function isOk(): bool
    {
        return $this->getStatusCode() === 200;
    }

    /**
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->getStatusCode() >= 200 && $this->getStatusCode() < 300;
    }

    /**
     * @return bool
     */
    public function isRedirect(): bool
    {
        return in_array($this->getStatusCode(), [301, 302, 303, 307]);
    }

    /**
     * @return bool
     */
    public function isRedirection(): bool
    {
        return $this->getStatusCode() >= 300 && $this->getStatusCode() < 400;
    }

    /**
     * @return bool
     */
    public function isForbidden(): bool
    {
        return $this->getStatusCode() === 403;
    }

    /**
     * @return bool
     */
    public function isNotFound(): bool
    {
        return $this->getStatusCode() === 404;
    }

    /**
     * @return bool
     */
    public function isClientError()
    {
        return $this->getStatusCode() >= 400 && $this->getStatusCode() < 500;
    }

    /**
     * @return bool
     */
    public function isServerError(): bool
    {
        return $this->getStatusCode() >= 500 && $this->getStatusCode() < 600;
    }


    /**
     * set the headers and return the response body to be sent
     * @param bool|null $output should the request output itself on the stdout of php
     * @return string the text response
     */
    public function send(?bool $output = false): string
    {
        $http_line = sprintf('%s %s %s',
            $this->getProtocol(),
            $this->getStatusCode(),
            $this->getReasonPhrase()
        );

        header($http_line, true, $this->getStatusCode());

        foreach ($this->getHeaders() as $name => $value) {
            header("$name: $value", false);
        }

        $body = $this->getBody();

        header(self::CONTENT_LENGTH . ": " . strlen($body), true);

        if($output){
            echo $body;
        }

        return $body;
    }

    public function __toString(): string
    {
        return $this->send();
    }
}