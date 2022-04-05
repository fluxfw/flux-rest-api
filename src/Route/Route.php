<?php

namespace FluxRestApi\Route;

use FluxRestApi\Body\BodyType;
use FluxRestApi\Method\Method;
use FluxRestApi\Request\RequestDto;
use FluxRestApi\Response\ResponseDto;

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


    public function handle(RequestDto $request) : ?ResponseDto;
}
