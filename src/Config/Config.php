<?php

namespace Fluxlabs\FluxRestApi\Config;

use Fluxlabs\FluxRestApi\Authorization\Authorization;
use Fluxlabs\FluxRestApi\Route\Collector\RouteCollector;

interface Config
{

    public function getAuthorization() : ?Authorization;


    public function getRouteCollector() : RouteCollector;
}
