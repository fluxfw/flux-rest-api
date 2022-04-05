<?php

namespace FluxRestApi\Adapter\Route;

use FluxRestApi\Body\JsonBodyDto;
use FluxRestApi\Method\LegacyDefaultMethod;
use FluxRestApi\Method\Method;
use FluxRestApi\Request\RequestDto;
use FluxRestApi\Response\ResponseDto;
use FluxRestApi\Route\Route;

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


    public function handle(RequestDto $request) : ?ResponseDto
    {
        return ResponseDto::new(
            JsonBodyDto::new(
                ($this->get_routes)()
            )
        );
    }
}
