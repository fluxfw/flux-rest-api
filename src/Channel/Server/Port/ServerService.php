<?php

namespace FluxRestApi\Channel\Server\Port;

use FluxRestApi\Adapter\Authorization\Authorization;
use FluxRestApi\Adapter\Route\Collector\RouteCollector;
use FluxRestApi\Adapter\Server\ServerRawRequestDto;
use FluxRestApi\Adapter\Server\ServerRawResponseDto;
use FluxRestApi\Adapter\Server\SwooleServerConfigDto;
use FluxRestApi\Channel\Body\Port\BodyService;
use FluxRestApi\Channel\Server\Command\HandleDefaultRequestCommand;
use FluxRestApi\Channel\Server\Command\HandleRequestCommand;
use FluxRestApi\Channel\Server\Command\InitSwooleServerCommand;

class ServerService
{

    private ?Authorization $authorization;
    private BodyService $body_service;
    private RouteCollector $route_collector;


    private function __construct(
        /*private readonly*/ BodyService $body_service,
        /*private readonly*/ RouteCollector $route_collector,
        /*private readonly*/ ?Authorization $authorization
    ) {
        $this->body_service = $body_service;
        $this->route_collector = $route_collector;
        $this->authorization = $authorization;
    }


    public static function new(
        BodyService $body_service,
        RouteCollector $route_collector,
        ?Authorization $authorization = null
    ) : /*static*/ self
    {
        return new static(
            $body_service,
            $route_collector,
            $authorization
        );
    }


    public function handleDefaultRequest() : void
    {
        HandleDefaultRequestCommand::new(
            $this,
            $this->body_service
        )
            ->handleDefaultRequest();
    }


    public function handleRequest(ServerRawRequestDto $request, bool $routes_ui = false) : ServerRawResponseDto
    {
        return HandleRequestCommand::new(
            $this->body_service,
            $this->route_collector,
            $this->authorization,
            $routes_ui
        )
            ->handleRequest(
                $request
            );
    }


    public function initSwooleServer(?SwooleServerConfigDto $swoole_server_config = null) : void
    {
        InitSwooleServerCommand::new(
            $this,
            $swoole_server_config
        )
            ->initSwooleServer();
    }
}
