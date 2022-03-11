<?php

namespace FluxRestApi\Route;

class MatchedRouteDto
{

    private array $params;
    private Route $route;


    private function __construct(
        /*public readonly*/ Route $route,
        /*public readonly*/ array $params
    ) {
        $this->route = $route;
        $this->params = $params;
    }


    public static function new(
        Route $route,
        ?array $params
    ) : /*static*/ self
    {
        return new static(
            $route,
            $params ?? []
        );
    }


    public function getParams() : array
    {
        return $this->params;
    }


    public function getRoute() : Route
    {
        return $this->route;
    }
}
