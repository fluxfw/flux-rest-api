<?php

namespace Fluxlabs\FluxRestApi\Route\Example;

use Fluxlabs\FluxRestApi\Body\BodyDto;
use Fluxlabs\FluxRestApi\Body\BodyType;
use Fluxlabs\FluxRestApi\Request\RequestDto;
use Fluxlabs\FluxRestApi\Response\ResponseDto;
use Fluxlabs\FluxRestApi\Route\Route;

class PostExampleRoute implements Route
{

    public static function new() : /*static*/ self
    {
        $route = new static();

        return $route;
    }


    public function getRoute() : string
    {
        return "/example/post";
    }


    public function getMethod() : string
    {
        return "POST";
    }


    public function getBodyType() : ?string
    {
        return BodyType::JSON;
    }


    public function handle(RequestDto $request) : ResponseDto
    {
        return ResponseDto::new(
            BodyDto::new(
                BodyType::JSON,
                [
                    "post_data" => $request->getParsedBody()->getBody()
                ]
            )
        );
    }
}
