<?php

namespace Fluxlabs\FluxRestApi\Route\Fetcher;

class StaticRoutesFetcher implements RoutesFetcher
{

    private array $routes;


    public static function new(array $routes) : /*static*/ self
    {
        $fetcher = new static();

        $fetcher->routes = $routes;

        return $fetcher;
    }


    public function fetchRoutes() : array
    {
        return $this->routes;
    }
}
