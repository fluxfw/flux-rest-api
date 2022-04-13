<?php

namespace FluxRestApi\Adapter\Route;

use FluxRestApi\Adapter\Body\Type\BodyType;
use FluxRestApi\Adapter\Method\Method;
use FluxRestApi\Adapter\Server\ServerRequestDto;
use FluxRestApi\Adapter\Server\ServerResponseDto;

interface Route
{

    /**
     * @return BodyType[]|null
     */
    public function getDocuRequestBodyTypes() : ?array;


    /**
     * @return string[]|null
     */
    public function getDocuRequestQueryParams() : ?array;


    public function getMethod() : Method;


    public function getRoute() : string;


    public function handle(ServerRequestDto $request) : ?ServerResponseDto;
}
