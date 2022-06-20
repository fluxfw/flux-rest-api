<?php

namespace FluxRestApi\Service\Server\Route;

use FluxRestApi\Adapter\Body\Type\DefaultBodyType;
use FluxRestApi\Adapter\Header\DefaultHeaderKey;
use FluxRestApi\Adapter\Method\DefaultMethod;
use FluxRestApi\Adapter\Method\Method;
use FluxRestApi\Adapter\Route\Documentation\RouteDocumentationDto;
use FluxRestApi\Adapter\Route\Documentation\RouteResponseDocumentationDto;
use FluxRestApi\Adapter\Route\Route;
use FluxRestApi\Adapter\Server\ServerRequestDto;
use FluxRestApi\Adapter\Server\ServerResponseDto;
use FluxRestApi\Adapter\ServerType\ServerType;
use FluxRestApi\Adapter\Status\DefaultStatus;

class GetRoutesUIDefaultRoute implements Route
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
            "Routes UI",
            null,
            null,
            null,
            null,
            [
                RouteResponseDocumentationDto::new(
                    DefaultBodyType::HTML,
                    null,
                    null,
                    "Routes UI"
                ),
                RouteResponseDocumentationDto::new(
                    null,
                    DefaultStatus::_302,
                    null,
                    "Redirect if has trailing / and remove it"
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
        return "/routes/ui";
    }


    public function handle(ServerRequestDto $request) : ?ServerResponseDto
    {
        if (str_ends_with($request->original_route, "/")) {
            return ServerResponseDto::new(
                null,
                DefaultStatus::_302,
                [
                    DefaultHeaderKey::LOCATION->value => rtrim($request->original_route, "/")
                ]
            );
        }

        $path = "/ui/index.html";

        return ServerResponseDto::new(
            null,
            null,
            null,
            null,
            $request->server_type === ServerType::NGINX ? "/flux-rest-api/routes" . $path : __DIR__ . $path
        );
    }
}
