<?php

namespace FluxRestApi\Request;

use FluxRestApi\Body\BodyDto;
use FluxRestApi\Libs\FluxRestBaseApi\Method\Method;
use FluxRestApi\Server\Server;

class RequestDto
{

    private array $cookies;
    private array $headers;
    private Method $method;
    private array $params;
    private ?BodyDto $parsed_body;
    private array $query_params;
    private ?string $raw_body;
    private string $route;
    private Server $server;


    private function __construct(
        /*public readonly*/ string $route,
        /*public readonly*/ Method $method,
        /*public readonly*/ Server $server,
        /*public readonly*/ array $query_params,
        /*public readonly*/ ?string $raw_body,
        /*public readonly*/ array $headers,
        /*public readonly*/ array $cookies,
        /*public readonly*/ array $params,
        /*public readonly*/ ?BodyDto $parsed_body
    ) {
        $this->route = $route;
        $this->method = $method;
        $this->server = $server;
        $this->query_params = $query_params;
        $this->raw_body = $raw_body;
        $this->headers = $headers;
        $this->cookies = $cookies;
        $this->params = $params;
        $this->parsed_body = $parsed_body;
    }


    public static function new(
        string $route,
        Method $method,
        Server $server,
        ?array $query_params = null,
        ?string $raw_body = null,
        ?array $headers = null,
        ?array $cookies = null,
        ?array $params = null,
        ?BodyDto $parsed_body = null
    ) : /*static*/ self
    {
        return new static(
            $route,
            $method,
            $server,
            $query_params ?? [],
            $raw_body,
            $headers ?? [],
            $cookies ?? [],
            $params ?? [],
            $parsed_body
        );
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


    public function getMethod() : Method
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


    public function getServer() : Server
    {
        return $this->server;
    }
}
