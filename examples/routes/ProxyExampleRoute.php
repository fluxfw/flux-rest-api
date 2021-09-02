<?php

namespace Fluxlabs\FluxRestApi\Route\Example;

use Fluxlabs\FluxRestApi\Adapter\Route\ProxyRoute;
use Fluxlabs\FluxRestApi\Method\Method;
use Fluxlabs\FluxRestApi\Route\Route;

class ProxyExampleRoute implements Route
{

    use ProxyRoute;

    public static function new() : /*static*/ self
    {
        $route = new static();

        return $route;
    }


    public function getDocuRequestBodyTypes() : ?array
    {
        return null;
    }


    public function getDocuRequestQueryParams() : ?array
    {
        return null;
    }


    public function getMethod() : string
    {
        return Method::GET;
    }


    public function getRoute() : string
    {
        return "/example/proxy";
    }


    protected function getProxyUrl() : string
    {
        return "http://internal-service/get-something";
    }
}
