<?php

namespace FluxRestApi\Service\Server\Route;

use FluxRestApi\Adapter\Body\Type\CustomBodyType;
use FluxRestApi\Adapter\Method\DefaultMethod;
use FluxRestApi\Adapter\Method\Method;
use FluxRestApi\Adapter\Route\Documentation\RouteDocumentationDto;
use FluxRestApi\Adapter\Route\Documentation\RouteParamDocumentationDto;
use FluxRestApi\Adapter\Route\Documentation\RouteResponseDocumentationDto;
use FluxRestApi\Adapter\Route\Route;
use FluxRestApi\Adapter\Server\ServerRequestDto;
use FluxRestApi\Adapter\Server\ServerResponseDto;
use FluxRestApi\Adapter\ServerType\ServerType;
use FluxRestApi\Adapter\Status\DefaultStatus;

class GetRoutesUIFileRoute implements Route
{

    private function __construct()
    {

    }


    public static function new() : static
    {
        return new static();
    }


    public function getDocumentation() : ?RouteDocumentationDto
    {
        return RouteDocumentationDto::new(
            $this->getRoute(),
            $this->getMethod(),
            "Get routes UI file",
            null,
            [
                RouteParamDocumentationDto::new(
                    "path",
                    "string",
                    "File path"
                )
            ],
            null,
            null,
            [
                RouteResponseDocumentationDto::new(
                    CustomBodyType::factory(
                        "*"
                    ),
                    null,
                    null,
                    "Routes UI file"

                ),
                RouteResponseDocumentationDto::new(
                    null,
                    DefaultStatus::_404,
                    null,
                    "Routes UI file not found"
                )
            ]
        );
    }


    public function getMethod() : Method
    {
        return DefaultMethod::GET;
    }


    public function getRoute() : string
    {
        return "/routes/ui/{path.}";
    }


    public function handle(ServerRequestDto $request) : ?ServerResponseDto
    {
        $path = "/ui/" . $request->getParam(
                "path"
            );

        if (file_exists(__DIR__ . $path)) {
            return ServerResponseDto::new(
                null,
                null,
                null,
                null,
                $request->server_type === ServerType::NGINX ? "/flux-rest-api/routes" . $path : __DIR__ . $path
            );
        } else {
            return ServerResponseDto::new(
                null,
                DefaultStatus::_404
            );
        }
    }
}
