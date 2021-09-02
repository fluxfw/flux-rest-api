<?php

namespace Fluxlabs\FluxRestApi\Route;

use Fluxlabs\FluxRestApi\Request\RequestDto;
use Fluxlabs\FluxRestApi\Response\ResponseDto;

interface Route
{

    public function getDocuRequestBodyTypes() : ?array;


    public function getDocuRequestQueryParams() : ?array;


    public function getMethod() : string;


    public function getRoute() : string;


    public function handle(RequestDto $request) : ?ResponseDto;
}
