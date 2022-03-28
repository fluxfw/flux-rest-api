<?php

namespace FluxRestApi\Adapter\Server;

use FluxRestApi\Adapter\Api\RestApi;
use FluxRestApi\Authorization\Authorization;
use FluxRestApi\Collector\RouteCollector;
use FluxRestApi\Libs\FluxRestBaseApi\Method\CustomMethod;
use FluxRestApi\Request\RawRequestDto;
use FluxRestApi\Response\ResponseDto;
use FluxRestApi\Server\LegacyDefaultServer;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

class SwooleRestApiServer
{

    private RestApi $rest_api;
    private SwooleRestApiServerConfigDto $swoole_rest_api_server_config;


    private function __construct(
        /*private readonly*/ RestApi $rest_api,
        /*private readonly*/ SwooleRestApiServerConfigDto $swoole_rest_api_server_config
    ) {
        $this->rest_api = $rest_api;
        $this->swoole_rest_api_server_config = $swoole_rest_api_server_config;
    }


    public static function new(
        RouteCollector $route_collector,
        ?Authorization $authorization = null,
        ?SwooleRestApiServerConfigDto $swoole_rest_api_server_config = null
    ) : /*static*/ self
    {
        return new static(
            RestApi::new(
                $route_collector,
                $authorization
            ),
            $swoole_rest_api_server_config ?? SwooleRestApiServerConfigDto::new()
        );
    }


    public function init() : void
    {
        $options = [];
        $sock_type = SWOOLE_TCP;

        if ($this->swoole_rest_api_server_config->getMaxUploadSize() !== null) {
            $options["package_max_length"] = $this->swoole_rest_api_server_config->getMaxUploadSize();
        }

        if ($this->swoole_rest_api_server_config->getHttpsCert() !== null) {
            $options += [
                "ssl_cert_file" => $this->swoole_rest_api_server_config->getHttpsCert(),
                "ssl_key_file"  => $this->swoole_rest_api_server_config->getHttpsKey()
            ];
            $sock_type += SWOOLE_SSL;
        }

        $server = new Server($this->swoole_rest_api_server_config->getListen(), $this->swoole_rest_api_server_config->getPort(), SWOOLE_PROCESS, $sock_type);

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
            $this->rest_api->handleRequest(
                $this->parseRequest(
                    $request
                )
            )
        );
    }


    private function handleResponse(Response $response, ResponseDto $api_response) : void
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

        if ($api_response->getRawBody() !== null) {
            $response->write($api_response->getRawBody());
        }

        if ($api_response->getSendfile() !== null) {
            $response->sendfile($api_response->getSendfile());

            return;
        }

        $response->end();
    }


    private function parseRequest(Request $request) : RawRequestDto
    {
        return RawRequestDto::new(
            $request->server["request_uri"],
            CustomMethod::factory($request->getMethod()),
            LegacyDefaultServer::SWOOLE(),
            $request->get,
            $request->getContent() ?: null,
            $request->post,
            $request->files,
            $request->header,
            $request->cookie
        );
    }
}
