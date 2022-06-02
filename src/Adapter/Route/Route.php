<?php

namespace FluxRestApi\Adapter\Route;

use FluxRestApi\Adapter\Method\Method;
use FluxRestApi\Adapter\Route\Documentation\RouteDocumentationDto;
use FluxRestApi\Adapter\Server\ServerRequestDto;
use FluxRestApi\Adapter\Server\ServerResponseDto;

interface Route
{

    public function getDocumentation() : ?RouteDocumentationDto;


    public function getMethod() : Method;


    public function getRoute() : string;


    public function handle(ServerRequestDto $request) : ?ServerResponseDto;
}
