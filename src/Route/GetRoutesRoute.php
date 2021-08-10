<?php

namespace Fluxlabs\FluxRestApi\Route;

use Fluxlabs\FluxRestApi\Body\JsonBodyDto;
use Fluxlabs\FluxRestApi\Request\RequestDto;
use Fluxlabs\FluxRestApi\Response\ResponseDto;

class GetRoutesRoute implements Route
{

    private $get_routes;


    public static function new(callable $get_routes) : /*static*/ self
    {
        $route = new static();

        $route->get_routes = $get_routes;

        return $route;
    }


    public function getBodyType() : ?string
    {
        return null;
    }


    public function getMethod() : string
    {
        return "GET";
    }


    public function getRoute() : string
    {
        return "/routes";
    }


    public function handle(RequestDto $request) : ResponseDto
    {
        return ResponseDto::new(
            JsonBodyDto::new(
                ($this->get_routes)()
            )
        );
    }
}
