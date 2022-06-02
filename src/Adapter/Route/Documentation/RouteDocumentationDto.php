<?php

namespace FluxRestApi\Adapter\Route\Documentation;

use FluxRestApi\Adapter\Method\LegacyDefaultMethod;
use FluxRestApi\Adapter\Method\Method;
use JsonSerializable;

class RouteDocumentationDto implements JsonSerializable
{

    /**
     * @var RouteContentTypeDocumentationDto[]
     */
    private array $content_types;
    private string $description;
    private Method $method;
    /**
     * @var RouteParamDocumentationDto[]
     */
    private array $query_params;
    /**
     * @var RouteResponseDocumentationDto[]
     */
    private array $responses;
    private string $route;
    /**
     * @var RouteParamDocumentationDto[]
     */
    private array $route_params;
    private string $title;


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
    ) : /*static*/ self
    {
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


    /**
     * @return RouteContentTypeDocumentationDto[]
     */
    public function getContentTypes() : array
    {
        return $this->content_types;
    }


    public function getDescription() : string
    {
        return $this->description;
    }


    public function getMethod() : Method
    {
        return $this->method;
    }


    /**
     * @return RouteParamDocumentationDto[]
     */
    public function getQueryParams() : array
    {
        return $this->query_params;
    }


    /**
     * @return RouteResponseDocumentationDto[]
     */
    public function getResponses() : array
    {
        return $this->responses;
    }


    public function getRoute() : string
    {
        return $this->route;
    }


    /**
     * @return RouteParamDocumentationDto[]
     */
    public function getRouteParams() : array
    {
        return $this->route_params;
    }


    public function getTitle() : string
    {
        return $this->title;
    }


    public function jsonSerialize() : object
    {
        return (object) [
            "content_types" => $this->content_types,
            "description"   => $this->description,
            "method"        => $this->method,
            "query_params"  => $this->query_params,
            "responses"     => $this->responses,
            "route"         => $this->route,
            "route_params"  => $this->route_params,
            "title"         => $this->title
        ];
    }
}
