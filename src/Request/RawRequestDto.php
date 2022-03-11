<?php

namespace FluxRestApi\Request;

use FluxRestApi\Libs\FluxRestBaseApi\Method\Method;
use FluxRestApi\Server\Server;

class RawRequestDto
{

    private ?string $body;
    private array $cookies;
    private array $files;
    private array $headers;
    private Method $method;
    private array $post;
    private array $query_params;
    private string $route;
    private Server $server;


    private function __construct(
        /*public readonly*/ string $route,
        /*public readonly*/ Method $method,
        /*public readonly*/ Server $server,
        /*public readonly*/ array $query_params,
        /*public readonly*/ ?string $body,
        /*public readonly*/ array $post,
        /*public readonly*/ array $files,
        /*public readonly*/ array $headers,
        /*public readonly*/ array $cookies
    ) {
        $this->route = $route;
        $this->method = $method;
        $this->server = $server;
        $this->query_params = $query_params;
        $this->body = $body;
        $this->post = $post;
        $this->files = $files;
        $this->headers = $headers;
        $this->cookies = $cookies;
    }


    public static function new(
        string $route,
        Method $method,
        Server $server,
        ?array $query_params = null,
        ?string $body = null,
        ?array $post = null,
        ?array $files = null,
        ?array $headers = null,
        ?array $cookies = null
    ) : /*static*/ self
    {
        return new static(
            $route,
            $method,
            $server,
            $query_params ?? [],
            $body,
            $post ?? [],
            $files ?? [],
            $headers ?? [],
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
        foreach ($this->headers as $key_ => $value) {
            if (strtolower($key_) === strtolower($key)) {
                return $value;
            }
        }

        return null;
    }


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


    public function getQueryParams() : array
    {
        return $this->query_params;
    }


    public function getRoute() : string
    {
        return $this->route;
    }


    public function getServer() : Server
    {
        return $this->server;
    }
}
