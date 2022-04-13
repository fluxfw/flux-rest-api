<?php

namespace FluxRestApi\Channel\Server\Route;

use FluxRestApi\Adapter\Body\JsonBodyDto;
use FluxRestApi\Adapter\Method\LegacyDefaultMethod;
use FluxRestApi\Adapter\Method\Method;
use FluxRestApi\Adapter\Route\Route;
use FluxRestApi\Adapter\Server\ServerRequestDto;
use FluxRestApi\Adapter\Server\ServerResponseDto;

class GetRoutesRoute implements Route
{

    /**
     * @var callable
     */
    private $get_routes;


    /**
     * @param callable $get_routes
     */
    private function __construct(
        /*private readonly mixed*/ callable $get_routes
    ) {
        $this->get_routes = $get_routes;
    }


    public static function new(
        callable $get_routes
    ) : /*static*/ self
    {
        return new static(
            $get_routes
        );
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
        return "/routes";
    }


    public function handle(ServerRequestDto $request) : ?ServerResponseDto
    {
        return ServerResponseDto::new(
            JsonBodyDto::new(
                ($this->get_routes)()
            )
        );
    }
}
