<?php

namespace FluxRestApi\Route;

class MatchedRouteDto
{

    private array $params;
    private Route $route;


    public static function new(Route $route, ?array $params) : /*static*/ self
    {
        $dto = new static();

        $dto->route = $route;
        $dto->params = $params ?? [];

        return $dto;
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
