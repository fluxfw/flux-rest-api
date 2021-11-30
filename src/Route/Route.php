<?php

namespace FluxRestApi\Route;

use FluxRestApi\Request\RequestDto;
use FluxRestApi\Response\ResponseDto;
use FluxRestBaseApi\Method\Method;

interface Route
{

    public function getDocuRequestBodyTypes() : ?array;


    public function getDocuRequestQueryParams() : ?array;


    public function getMethod() : Method;


    public function getRoute() : string;


    public function handle(RequestDto $request) : ?ResponseDto;
}
