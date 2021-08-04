<?php

namespace Fluxlabs\FluxRestApi\Route\Collector;

class StaticRouteCollector implements RouteCollector
{

    private array $routes;


    public static function new(array $routes) : /*static*/ self
    {
        $collector = new static();

        $collector->routes = $routes;

        return $collector;
    }


    public function collectRoutes() : array
    {
        return $this->routes;
    }
}
