<?php

namespace Fluxlabs\FluxRestApi\Request;

class RawRequestDto
{

    private ?string $body;
    private array $cookies;
    private array $headers;
    private string $method;
    private ?array $query;
    private string $route;


    public static function new(string $route, string $method, ?array $query, ?string $body, ?array $headers, ?array $cookies) : /*static*/ self
    {
        $dto = new static();

        $dto->route = $route;
        $dto->method = $method;
        $dto->query = $query;
        $dto->body = $body;
        $dto->cookies = $cookies ?? [];

        $dto->headers = $headers ?? [];

        return $dto;
    }


    public function getBody() : ?string
    {
        return $this->body;
    }


    public function getCookies() : array
    {
        return $this->cookies;
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


    public function getQuery() : ?array
    {
        return $this->query;
    }


    public function getRoute() : string
    {
        return $this->route;
    }
}
