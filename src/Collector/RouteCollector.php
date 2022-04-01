<?php

namespace FluxRestApi\Collector;

use FluxRestApi\Route\Route;

interface RouteCollector
{

    /**
     * @return Route[]
     */
    public function collectRoutes() : array;
}
