<?php

namespace Fluxlabs\FluxRestApi\Route\Example;

use Fluxlabs\FluxRestApi\Route\ProxyRoute;
use Fluxlabs\FluxRestApi\Route\Route;

class ProxyExampleRoute implements Route
{

    use ProxyRoute;

    public static function new() : /*static*/ self
    {
        $route = new static();

        return $route;
    }


    public function getRoute() : string
    {
        return "/example/proxy";
    }


    public function getMethod() : string
    {
        return "GET";
    }


    public function getBodyType() : ?string
    {
        return null;
    }


    protected function getProxyUrl() : string
    {
        return "http://internal-service/get-something";
    }
}
