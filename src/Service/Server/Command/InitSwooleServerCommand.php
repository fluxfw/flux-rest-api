<?php

namespace FluxRestApi\Service\Server\Command;

use FluxRestApi\Adapter\Method\CustomMethod;
use FluxRestApi\Adapter\Server\ServerRawRequestDto;
use FluxRestApi\Adapter\Server\ServerRawResponseDto;
use FluxRestApi\Adapter\Server\SwooleServerConfigDto;
use FluxRestApi\Adapter\ServerType\LegacyDefaultServerType;
use FluxRestApi\Service\Server\Port\ServerService;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

class InitSwooleServerCommand
{

    private readonly ServerService $server_service;
    private readonly SwooleServerConfigDto $swoole_server_config;


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
    ) : static {
        return new static(
            $server_service,
            $swoole_server_config ?? SwooleServerConfigDto::new()
        );
    }


    public function initSwooleServer() : void
    {
        $options = [];
        $sock_type = SWOOLE_TCP;

        if ($this->swoole_server_config->max_upload_size !== null) {
            $options["package_max_length"] = $this->swoole_server_config->max_upload_size;
        }

        if ($this->swoole_server_config->https_cert !== null) {
            $options += [
                "ssl_cert_file" => $this->swoole_server_config->https_cert,
                "ssl_key_file"  => $this->swoole_server_config->https_key
            ];
            $sock_type += SWOOLE_SSL;
        }

        $server = new Server($this->swoole_server_config->listen, $this->swoole_server_config->port, SWOOLE_PROCESS, $sock_type);

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
                ),
                true
            )
        );
    }


    private function handleResponse(Response $response, ServerRawResponseDto $api_response) : void
    {
        $response->status($api_response->status->value);

        foreach ($api_response->headers as $key => $value) {
            $response->header($key, $value);
        }

        foreach ($api_response->cookies as $cookie) {
            if ($cookie->value !== null) {
                $response->rawcookie(
                    $cookie->name,
                    $cookie->value,
                    $cookie->expires_in !== null ? (time() + $cookie->expires_in) : 0,
                    $cookie->path,
                    $cookie->domain,
                    $cookie->secure,
                    $cookie->http_only,
                    $cookie->same_site !== null ? $cookie->same_site->value : "",
                    $cookie->priority !== null ? $cookie->priority->value : ""
                );
            } else {
                $response->rawcookie(
                    $cookie->name,
                    "",
                    0,
                    $cookie->path,
                    $cookie->domain
                );
            }
        }

        if ($api_response->body !== null) {
            $response->write($api_response->body);
        }

        if ($api_response->sendfile !== null) {
            $response->sendfile($api_response->sendfile);

            return;
        }

        $response->end();
    }


    private function parseRequest(Request $request) : ServerRawRequestDto
    {
        return ServerRawRequestDto::new(
            $request->server["request_uri"],
            $request->server["request_uri"],
            CustomMethod::factory(
                $request->getMethod()
            ),
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
