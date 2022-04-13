<?php

namespace FluxRestApi\Channel\Server\Command;

use FluxRestApi\Adapter\Method\CustomMethod;
use FluxRestApi\Adapter\Server\ServerRawRequestDto;
use FluxRestApi\Adapter\Server\ServerRawResponseDto;
use FluxRestApi\Adapter\Server\SwooleServerConfigDto;
use FluxRestApi\Adapter\ServerType\LegacyDefaultServerType;
use FluxRestApi\Channel\Server\Port\ServerService;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

class InitSwooleServerCommand
{

    private ServerService $server_service;
    private SwooleServerConfigDto $swoole_server_config;


    private function __construct(
        /*private readonly*/ ServerService $server_service,
        /*private readonly*/ SwooleServerConfigDto $swoole_server_config
    ) {
        $this->server_service = $server_service;
        $this->swoole_server_config = $swoole_server_config;
    }


    public static function new(
        ServerService $server_service,
        ?SwooleServerConfigDto $swoole_server_config = null
    ) : /*static*/ self
    {
        return new static(
            $server_service,
            $swoole_server_config ?? SwooleServerConfigDto::new()
        );
    }


    public function initSwooleServer() : void
    {
        $options = [];
        $sock_type = SWOOLE_TCP;

        if ($this->swoole_server_config->getMaxUploadSize() !== null) {
            $options["package_max_length"] = $this->swoole_server_config->getMaxUploadSize();
        }

        if ($this->swoole_server_config->getHttpsCert() !== null) {
            $options += [
                "ssl_cert_file" => $this->swoole_server_config->getHttpsCert(),
                "ssl_key_file"  => $this->swoole_server_config->getHttpsKey()
            ];
            $sock_type += SWOOLE_SSL;
        }

        $server = new Server($this->swoole_server_config->getListen(), $this->swoole_server_config->getPort(), SWOOLE_PROCESS, $sock_type);

        $server->set($options);

        $server->on("request", function (Request $request, Response $response) : void {
            $this->handle(
                $request,
                $response
            );
        });

        $server->start();
    }


    private function handle(Request $request, Response $response) : void
    {
        $this->handleResponse(
            $response,
            $this->server_service->handleRequest(
                $this->parseRequest(
                    $request
                )
            )
        );
    }


    private function handleResponse(Response $response, ServerRawResponseDto $api_response) : void
    {
        $response->status($api_response->getStatus()->value);

        foreach ($api_response->getHeaders() as $key => $value) {
            $response->header($key, $value);
        }

        foreach ($api_response->getCookies() as $cookie) {
            if ($cookie->getValue() !== null) {
                $response->cookie(
                    $cookie->getName(),
                    $cookie->getValue(),
                    $cookie->getExpiresIn() !== null ? (time() + $cookie->getExpiresIn()) : 0,
                    $cookie->getPath(),
                    $cookie->getDomain(),
                    $cookie->isSecure(),
                    $cookie->isHttpOnly(),
                    $cookie->getSameSite() !== null ? $cookie->getSameSite()->value : "",
                    $cookie->getPriority() !== null ? $cookie->getPriority()->value : ""
                );
            } else {
                $response->cookie(
                    $cookie->getName(),
                    "",
                    0,
                    $cookie->getPath(),
                    $cookie->getDomain()
                );
            }
        }

        if ($api_response->getBody() !== null) {
            $response->write($api_response->getBody());
        }

        if ($api_response->getSendfile() !== null) {
            $response->sendfile($api_response->getSendfile());

            return;
        }

        $response->end();
    }


    private function parseRequest(Request $request) : ServerRawRequestDto
    {
        return ServerRawRequestDto::new(
            $request->server["request_uri"],
            CustomMethod::factory($request->getMethod()),
            LegacyDefaultServerType::SWOOLE(),
            $request->get,
            $request->getContent() ?: null,
            $request->post,
            $request->files,
            $request->header,
            $request->cookie
        );
    }
}
