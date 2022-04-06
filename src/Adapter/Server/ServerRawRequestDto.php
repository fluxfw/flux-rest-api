<?php

namespace FluxRestApi\Adapter\Server;

use FluxRestApi\Adapter\Method\Method;
use FluxRestApi\Adapter\ServerType\ServerType;

class ServerRawRequestDto
{

    private ?string $body;
    /**
     * @var string[]
     */
    private array $cookies;
    private array $files;
    /**
     * @var string[]
     */
    private array $headers;
    private Method $method;
    private array $post;
    /**
     * @var string[]
     */
    private array $query_params;
    private string $route;
    private ServerType $server_type;


    /**
     * @param string[] $query_params
     * @param string[] $headers
     * @param string[] $cookies
     */
    private function __construct(
        /*public readonly*/ string $route,
        /*public readonly*/ Method $method,
        /*public readonly*/ ServerType $server_type,
        /*public readonly*/ array $query_params,
        /*public readonly*/ ?string $body,
        /*public readonly*/ array $post,
        /*public readonly*/ array $files,
        /*public readonly*/ array $headers,
        /*public readonly*/ array $cookies
    ) {
        $this->route = $route;
        $this->method = $method;
        $this->server_type = $server_type;
        $this->query_params = $query_params;
        $this->body = $body;
        $this->post = $post;
        $this->files = $files;
        $this->headers = $headers;
        $this->cookies = $cookies;
    }


    /**
     * @param string[]|null $query_params
     * @param string[]|null $headers
     * @param string[]|null $cookies
     */
    public static function new(
        string $route,
        Method $method,
        ServerType $server_type,
        ?array $query_params = null,
        ?string $body = null,
        ?array $post = null,
        ?array $files = null,
        ?array $headers = null,
        ?array $cookies = null
    ) : /*static*/ self
    {
        $headers ??= [];

        return new static(
            $route,
            $method,
            $server_type,
            $query_params ?? [],
            $body,
            $post ?? [],
            $files ?? [],
            array_combine(array_map("strtolower", array_keys($headers)), $headers),
            $cookies ?? []
        );
    }


    public function getBody() : ?string
    {
        return $this->body;
    }


    public function getCookie(string $name) : ?string
    {
        return $this->cookies[$name] ?? null;
    }


    /**
     * @return string[]
     */
    public function getCookies() : array
    {
        return $this->cookies;
    }


    public function getFiles() : array
    {
        return $this->files;
    }


    public function getHeader(string $key) : ?string
    {
        return $this->headers[strtolower($key)] ?? null;
    }


    /**
     * @return string[]
     */
    public function getHeaders() : array
    {
        return $this->headers;
    }


    public function getMethod() : Method
    {
        return $this->method;
    }


    public function getPost() : array
    {
        return $this->post;
    }


    public function getQueryParam(string $name) : ?string
    {
        return $this->query_params[$name] ?? null;
    }


    /**
     * @return string[]
     */
    public function getQueryParams() : array
    {
        return $this->query_params;
    }


    public function getRoute() : string
    {
        return $this->route;
    }


    public function getServerType() : ServerType
    {
        return $this->server_type;
    }
}
