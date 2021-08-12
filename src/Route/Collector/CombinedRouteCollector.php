<?php

namespace Fluxlabs\FluxRestApi\Route\Collector;

use Fluxlabs\FluxRestApi\Route\Route;
use LogicException;

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
        return array_reduce($this->route_collectors, function (array $routes, $route_collector) : array {
            switch (true) {
                case $route_collector instanceof RouteCollector:
                    $routes = array_merge($routes, $route_collector->collectRoutes());
                    break;

                case $route_collector instanceof Route:
                    $routes[] = $route_collector;
                    break;

                default:
                    throw new LogicException(get_class($route_collector) . " is not supported");
            }

            return $routes;
        }, []);
    }
}
