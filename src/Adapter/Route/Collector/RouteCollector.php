<?php

namespace FluxRestApi\Adapter\Route\Collector;

use FluxRestApi\Adapter\Route\Route;

interface RouteCollector
{

    /**
     * @return Route[]
     */
    public function collectRoutes() : array;
}
