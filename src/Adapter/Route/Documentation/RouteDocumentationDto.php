<?php

namespace FluxRestApi\Adapter\Route\Documentation;

use FluxRestApi\Adapter\Method\LegacyDefaultMethod;
use FluxRestApi\Adapter\Method\Method;

class RouteDocumentationDto
{

    /**
     * @var RouteContentTypeDocumentationDto[]
     */
    public array $content_types;
    public string $description;
    public Method $method;
    /**
     * @var RouteParamDocumentationDto[]
     */
    public array $query_params;
    /**
     * @var RouteResponseDocumentationDto[]
     */
    public array $responses;
    public string $route;
    /**
     * @var RouteParamDocumentationDto[]
     */
    public array $route_params;
    public string $title;


    /**
     * @param RouteParamDocumentationDto[]       $route_params
     * @param RouteParamDocumentationDto[]       $query_params
     * @param RouteContentTypeDocumentationDto[] $content_types
     * @param RouteResponseDocumentationDto[]    $responses
     */
    private function __construct(
        /*public readonly*/ string $route,
        /*public readonly*/ Method $method,
        /*public readonly*/ string $title,
        /*public readonly*/ string $description,
        /*public readonly*/ array $route_params,
        /*public readonly*/ array $query_params,
        /*public readonly*/ array $content_types,
        /*public readonly*/ array $responses
    ) {
        $this->route = $route;
        $this->method = $method;
        $this->title = $title;
        $this->description = $description;
        $this->route_params = $route_params;
        $this->query_params = $query_params;
        $this->content_types = $content_types;
        $this->responses = $responses;
    }


    /**
     * @param RouteParamDocumentationDto[]       $route_params
     * @param RouteParamDocumentationDto[]       $query_params
     * @param RouteContentTypeDocumentationDto[] $content_types
     * @param RouteResponseDocumentationDto[]    $responses
     */
    public static function new(
        string $route,
        ?Method $method = null,
        ?string $title = null,
        ?string $description = null,
        ?array $route_params = null,
        ?array $query_params = null,
        ?array $content_types = null,
        ?array $responses = null
    ) : static {
        return new static(
            $route,
            $method ?? LegacyDefaultMethod::GET(),
            $title ?? "",
            $description ?? "",
            $route_params ?? [],
            $query_params ?? [],
            $content_types ?? [],
            $responses ?? []
        );
    }
}
