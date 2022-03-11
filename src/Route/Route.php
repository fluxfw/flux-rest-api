<?php

namespace FluxRestApi\Route;

use FluxRestApi\Libs\FluxRestBaseApi\Method\Method;
use FluxRestApi\Request\RequestDto;
use FluxRestApi\Response\ResponseDto;

interface Route
{

    public function getDocuRequestBodyTypes() : ?array;


    public function getDocuRequestQueryParams() : ?array;


    public function getMethod() : Method;


    public function getRoute() : string;


    public function handle(RequestDto $request) : ?ResponseDto;
}
