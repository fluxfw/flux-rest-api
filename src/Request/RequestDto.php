<?php

namespace Fluxlabs\FluxRestApi\Request;

use Fluxlabs\FluxRestApi\Body\BodyDto;

class RequestDto
{

    private ?BodyDto $body;
    private array $cookies;
    private array $headers;
    private string $method;
    private array $params;
    private array $query;
    private string $route;


    public static function new(string $route, string $method, ?array $params, ?array $query, ?BodyDto $body, ?array $headers, ?array $cookies) : /*static*/ self
    {
        $dto = new static();

        $dto->route = $route;
        $dto->method = $method;
        $dto->params = $params ?? [];
        $dto->query = $query ?? [];
        $dto->body = $body;
        $dto->headers = $headers ?? [];
        $dto->cookies = $cookies ?? [];

        return $dto;
    }


    public function getBody() : ?BodyDto
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


    public function getParams() : array
    {
        return $this->params;
    }


    public function getQuery() : array
    {
        return $this->query;
    }


    public function getRoute() : string
    {
        return $this->route;
    }
}
