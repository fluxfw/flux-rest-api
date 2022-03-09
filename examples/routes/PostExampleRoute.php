<?php

namespace FluxRestApi\Route\Example;

use FluxRestApi\Body\JsonBodyDto;
use FluxRestApi\Body\TextBodyDto;
use FluxRestApi\Request\RequestDto;
use FluxRestApi\Response\ResponseDto;
use FluxRestApi\Route\Route;
use FluxRestApi\Libs\FluxRestBaseApi\Body\LegacyDefaultBodyType;
use FluxRestApi\Libs\FluxRestBaseApi\Method\LegacyDefaultMethod;
use FluxRestApi\Libs\FluxRestBaseApi\Method\Method;
use FluxRestApi\Libs\FluxRestBaseApi\Status\LegacyDefaultStatus;

class PostExampleRoute implements Route
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
        return [
            LegacyDefaultBodyType::JSON()
        ];
    }


    public function getDocuRequestQueryParams() : ?array
    {
        return null;
    }


    public function getMethod() : Method
    {
        return LegacyDefaultMethod::POST();
    }


    public function getRoute() : string
    {
        return "/example/post";
    }


    public function handle(RequestDto $request) : ?ResponseDto
    {
        if (!($request->getParsedBody() instanceof JsonBodyDto)) {
            return ResponseDto::new(
                TextBodyDto::new(
                    "No json body"
                ),
                LegacyDefaultStatus::_400()
            );
        }

        return ResponseDto::new(
            JsonBodyDto::new(
                [
                    "post_data" => $request->getParsedBody()->getData()
                ]
            )
        );
    }
}
