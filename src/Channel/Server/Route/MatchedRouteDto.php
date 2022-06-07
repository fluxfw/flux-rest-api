<?php

namespace FluxRestApi\Channel\Server\Route;

use FluxRestApi\Adapter\Route\Route;

class MatchedRouteDto
{

    /**
     * @var string[]
     */
    public array $params;
    public Route $route;


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
}
