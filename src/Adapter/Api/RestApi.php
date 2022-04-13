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
use FluxRestApi\Adapter\Server\SwooleServerConfigDto;
use FluxRestApi\Adapter\ServerType\ServerType;
use FluxRestApi\Channel\Body\Port\BodyService;
use FluxRestApi\Channel\Client\Port\ClientService;
use FluxRestApi\Channel\Server\Port\ServerService;

class RestApi
{

    private function __construct()
    {

    }


    public static function new() : /*static*/ self
    {
        return new static();
    }


    public function getDefaultRequest(?bool $rest_api_server = null) : ServerRawRequestDto
    {
        return $this->getBodyService()
            ->getDefaultRequest(
                $rest_api_server
            );
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


    public function handleRequest(ServerRawRequestDto $request, RouteCollector $route_collector, ?Authorization $authorization = null) : ServerRawResponseDto
    {
        return $this->getServerService(
            $route_collector,
            $authorization
        )
            ->handleRequest(
                $request
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
