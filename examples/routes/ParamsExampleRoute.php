<?php

namespace FluxRestApi\Route\Example;

use FluxRestApi\Body\JsonBodyDto;
use FluxRestApi\Method\Method;
use FluxRestApi\Request\RequestDto;
use FluxRestApi\Response\ResponseDto;
use FluxRestApi\Route\Route;

class ParamsExampleRoute implements Route
{

    public static function new() : /*static*/ self
    {
        $route = new static();

        return $route;
    }


    public function getDocuRequestBodyTypes() : ?array
    {
        return null;
    }


    public function getDocuRequestQueryParams() : ?array
    {
        return null;
    }


    public function getMethod() : string
    {
        return Method::GET;
    }


    public function getRoute() : string
    {
        return "/example/params/{param_1}/{param_2}";
    }


    public function handle(RequestDto $request) : ?ResponseDto
    {
        return ResponseDto::new(
            JsonBodyDto::new(
                [
                    "params"       => $request->getParams(),
                    "query_params" => $request->getQueryParams()
                ]
            )
        );
    }
}
