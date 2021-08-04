<?php

namespace Fluxlabs\FluxRestApi\Route\Collector;

class CombinedRouteCollector implements RouteCollector
{

    private array $route_collectors;


    public static function new(array $route_collectors) : /*static*/ self
    {
        $collector = new static();

        $collector->route_collectors = $route_collectors;

        return $collector;
    }


    public function collectRoutes() : array
    {
        return array_reduce($this->route_collectors, fn(array $routes, RouteCollector $route_collector) : array => array_merge($routes, $route_collector->collectRoutes()), []);
    }
}
