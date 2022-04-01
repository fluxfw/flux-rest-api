<?php

namespace FluxRestApi\Route;

class MatchedRouteDto
{

    /**
     * @var string[]
     */
    private array $params;
    private Route $route;


    /**
     * @param string[] $params
     */
    private function __construct(
        /*public readonly*/ Route $route,
        /*public readonly*/ array $params
    ) {
        $this->route = $route;
        $this->params = $params;
    }


    /**
     * @param string[]|null $params
     */
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


    /**
     * @return string[]
     */
    public function getParams() : array
    {
        return $this->params;
    }


    public function getRoute() : Route
    {
        return $this->route;
    }
}
