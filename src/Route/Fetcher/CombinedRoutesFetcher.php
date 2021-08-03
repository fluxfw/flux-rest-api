<?php

namespace Fluxlabs\FluxRestApi\Route\Fetcher;

class CombinedRoutesFetcher implements RoutesFetcher
{

    private array $routes_fetchers;


    public static function new(array $routes_fetchers) : /*static*/ self
    {
        $fetcher = new static();

        $fetcher->routes_fetchers = $routes_fetchers;

        return $fetcher;
    }


    public function fetchRoutes() : array
    {
        return array_reduce($this->routes_fetchers, fn(array $routes, RoutesFetcher $routes_fetcher) : array => array_merge($routes, $routes_fetcher->fetchRoutes()), []);
    }
}
