<?php

namespace Fluxlabs\FluxRestApi\Request;

class RawRequestDto
{

    private ?string $body;
    private array $cookies;
    private array $files;
    private array $headers;
    private string $method;
    private array $post;
    private array $query_params;
    private string $route;
    private string $server;


    public static function new(
        string $route,
        string $method,
        string $server,
        ?array $query_params = null,
        ?string $body = null,
        ?array $post = null,
        ?array $files = null,
        ?array $headers = null,
        ?array $cookies = null
    ) : /*static*/ self
    {
        $dto = new static();

        $dto->route = $route;
        $dto->method = $method;
        $dto->server = $server;
        $dto->query_params = $query_params ?? [];
        $dto->body = $body;
        $dto->post = $post ?? [];
        $dto->files = $files ?? [];
        $dto->headers = $headers ?? [];
        $dto->cookies = $cookies ?? [];

        return $dto;
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


    public function getMethod() : string
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


    public function getServer() : string
    {
        return $this->server;
    }
}
