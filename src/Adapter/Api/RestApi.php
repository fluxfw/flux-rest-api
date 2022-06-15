<?php

namespace FluxRestApi\Adapter\Api;

use FluxRestApi\Adapter\Authorization\Authorization;
use FluxRestApi\Adapter\Body\BodyDto;
use FluxRestApi\Adapter\Body\RawBodyDto;
use FluxRestApi\Adapter\Client\ClientRequestDto;
use FluxRestApi\Adapter\Client\ClientResponseDto;
use FluxRestApi\Adapter\Route\Collector\RouteCollector;
use FluxRestApi\Adapter\Server\ServerRawRequestDto;
use FluxRestApi\Adapter\Server\ServerRawResponseDto;
use FluxRestApi\Adapter\Server\ServerResponseDto;
use FluxRestApi\Adapter\Server\SwooleServerConfigDto;
use FluxRestApi\Adapter\ServerType\ServerType;
use FluxRestApi\Service\Body\Port\BodyService;
use FluxRestApi\Service\Client\Port\ClientService;
use FluxRestApi\Service\Server\Command\HandleMethodOverrideCommand;
use FluxRestApi\Service\Server\Port\ServerService;

class RestApi
{

    private function __construct()
    {

    }


    public static function new() : /*static*/ self
    {
        return new static();
    }


    public function getDefaultRequest() : ServerRawRequestDto
    {
        return $this->getBodyService()
            ->getDefaultRequest();
    }


    public function handleDefaultRequest(RouteCollector $route_collector, ?Authorization $authorization = null) : void
    {
        $this->getServerService(
            $route_collector,
            $authorization
        )
            ->handleDefaultRequest();
    }


    public function handleDefaultResponse(ServerRawResponseDto $response, ServerType $server_type) : void
    {
        $this->getBodyService()
            ->handleDefaultResponse(
                $response,
                $server_type
            );
    }


    /**
     * @return ServerRawRequestDto|ServerResponseDto|null
     */
    public function handleMethodOverride(ServerRawRequestDto $request)/* : ServerRawRequestDto|ServerResponseDto|null*/
    {
        return $this->getBodyService()
            ->handleMethodOverride(
                $request
            );
    }


    public function handleRequest(ServerRawRequestDto $request, RouteCollector $route_collector, ?Authorization $authorization = null, bool $routes_ui = false) : ServerRawResponseDto
    {
        return $this->getServerService(
            $route_collector,
            $authorization
        )
            ->handleRequest(
                $request,
                $routes_ui
            );
    }


    public function initSwooleServer(RouteCollector $route_collector, ?Authorization $authorization = null, ?SwooleServerConfigDto $swoole_server_config = null) : void
    {
        $this->getServerService(
            $route_collector,
            $authorization
        )
            ->initSwooleServer(
                $swoole_server_config
            );
    }


    public function makeRequest(ClientRequestDto $request) : ?ClientResponseDto
    {
        return $this->getClientService()
            ->makeRequest(
                $request
            );
    }


    public function parseBody(RawBodyDto $body, ?array $post = null, ?array $files = null) : ?BodyDto
    {
        return $this->getBodyService()
            ->parseBody(
                $body,
                $post,
                $files
            );
    }


    public function toRawBody(BodyDto $body) : RawBodyDto
    {
        return $this->getBodyService()
            ->toRawBody(
                $body
            );
    }


    private function getBodyService() : BodyService
    {
        return BodyService::new();
    }


    private function getClientService() : ClientService
    {
        return ClientService::new();
    }


    private function getServerService(RouteCollector $route_collector, ?Authorization $authorization = null) : ServerService
    {
        return ServerService::new(
            $this->getBodyService(),
            $route_collector,
            $authorization
        );
    }
}
