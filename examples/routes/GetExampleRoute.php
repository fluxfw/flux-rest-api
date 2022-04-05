<?php

namespace FluxRestApi\Route\Example;

use FluxRestApi\Body\JsonBodyDto;
use FluxRestApi\Method\LegacyDefaultMethod;
use FluxRestApi\Method\Method;
use FluxRestApi\Request\RequestDto;
use FluxRestApi\Response\ResponseDto;
use FluxRestApi\Route\Route;

class GetExampleRoute implements Route
{

    private function __construct()
    {

    }


    public static function new() : /*static*/ self
    {
        return new static();
    }


    public function getDocuRequestBodyTypes() : ?array
    {
        return null;
    }


    public function getDocuRequestQueryParams() : ?array
    {
        return null;
    }


    public function getMethod() : Method
    {
        return LegacyDefaultMethod::GET();
    }


    public function getRoute() : string
    {
        return "/example/get";
    }


    public function handle(RequestDto $request) : ?ResponseDto
    {
        return ResponseDto::new(
            JsonBodyDto::new(
                [
                    "Some test data",
                    1234,
                    true
                ]
            )
        );
    }
}
