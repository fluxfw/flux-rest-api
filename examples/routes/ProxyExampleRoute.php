<?php

namespace FluxRestApi\Route\Example;

use FluxRestApi\Adapter\Route\ProxyRoute;
use FluxRestApi\Route\Route;
use FluxRestApi\Libs\FluxRestBaseApi\Method\LegacyDefaultMethod;
use FluxRestApi\Libs\FluxRestBaseApi\Method\Method;

class ProxyExampleRoute implements Route
{

    use ProxyRoute;

    private function __construct()
    {

    }


    public static function new() : /*static*/ self
    {
        return new static();
    }


    public function getDocuRequestBodyTypes() : ?array
    {
        return null;
    }


    public function getDocuRequestQueryParams() : ?array
    {
        return null;
    }


    public function getMethod() : Method
    {
        return LegacyDefaultMethod::GET();
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
