<?php

namespace FluxRestApi\Request;

use FluxRestApi\Body\BodyDto;

class RequestDto
{

    private array $cookies;
    private array $headers;
    private string $method;
    private array $params;
    private ?BodyDto $parsed_body;
    private array $query_params;
    private ?string $raw_body;
    private string $route;
    private string $server;


    public static function new(
        string $route,
        string $method,
        string $server,
        ?array $query_params = null,
        ?string $raw_body = null,
        ?array $headers = null,
        ?array $cookies = null,
        ?array $params = null,
        ?BodyDto $parsed_body = null
    ) : /*static*/ self
    {
        $dto = new static();

        $dto->route = $route;
        $dto->method = $method;
        $dto->server = $server;
        $dto->query_params = $query_params ?? [];
        $dto->raw_body = $raw_body;
        $dto->headers = $headers ?? [];
        $dto->cookies = $cookies ?? [];
        $dto->params = $params ?? [];
        $dto->parsed_body = $parsed_body;

        return $dto;
    }


    public function getCookie(string $name) : ?string
    {
        return $this->cookies[$name] ?? null;
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


    public function getParam(string $name) : ?string
    {
        return $this->params[$name] ?? null;
    }


    public function getParams() : array
    {
        return $this->params;
    }


    public function getParsedBody() : ?BodyDto
    {
        return $this->parsed_body;
    }


    public function getQueryParam(string $name) : ?string
    {
        return $this->query_params[$name] ?? null;
    }


    public function getQueryParams() : array
    {
        return $this->query_params;
    }


    public function getRawBody() : ?string
    {
        return $this->raw_body;
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
