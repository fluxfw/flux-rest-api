<?php

namespace FluxRestApi\Adapter\Handler;

use FluxRestApi\Adapter\Api\Api;
use FluxRestApi\Authorization\Authorization;
use FluxRestApi\Collector\RouteCollector;
use FluxRestApi\Request\RawRequestDto;
use FluxRestApi\Response\ResponseDto;
use FluxRestApi\Server\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;

class SwooleHandler
{

    private Api $api;


    public static function new(RouteCollector $route_collector, ?Authorization $authorization = null) : /*static*/ self
    {
        $handler = new static();

        $handler->api = Api::new(
            $route_collector,
            $authorization
        );

        return $handler;
    }


    public function handle(Request $request, Response $response) : void
    {
        $this->handleResponse(
            $response,
            $this->api->handleRequest(
                $this->parseRequest(
                    $request
                )
            )
        );
    }


    private function handleResponse(Response $response, ResponseDto $api_response) : void
    {
        $response->status($api_response->getStatus());

        foreach ($api_response->getHeaders() as $key => $value) {
            $response->header($key, $value);
        }

        foreach ($api_response->getCookies() as $cookie) {
            if ($cookie->getValue() !== null) {
                $response->cookie(
                    $cookie->getName(),
                    $cookie->getValue(),
                    $cookie->getExpiresIn() !== null ? (time() + $cookie->getExpiresIn()) : null,
                    $cookie->getPath(),
                    $cookie->getDomain(),
                    $cookie->isSecure(),
                    $cookie->isHttpOnly(),
                    $cookie->getSameSite(),
                    $cookie->getPriority()
                );
            } else {
                $response->cookie(
                    $cookie->getName(),
                    null,
                    null,
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
            $request->getMethod(),
            Server::SWOOLE,
            $request->get,
            $request->getContent() ?: null,
            $request->post,
            $request->files,
            $request->header,
            $request->cookie
        );
    }
}
