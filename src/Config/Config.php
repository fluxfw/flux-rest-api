<?php

namespace Fluxlabs\FluxRestApi\Config;

use Fluxlabs\FluxRestApi\Authorization\Authorization;
use Fluxlabs\FluxRestApi\Route\Fetcher\RoutesFetcher;

interface Config
{

    public function getAuthorization() : Authorization;


    public function getRoutesFetcher() : RoutesFetcher;
}
