<?php

namespace Fluxlabs\FluxRestApi\Handler;

use Fluxlabs\FluxRestApi\Api\Api;
use Fluxlabs\FluxRestApi\Config\Config;
use Fluxlabs\FluxRestApi\Request\RawRequestDto;
use Fluxlabs\FluxRestApi\Response\RawResponseDto;
use Swoole\Http\Request;
use Swoole\Http\Response;

class SwooleHandler
{

    private Api $api;
    private Config $config;


    public static function new(Config $config) : /*static*/ self
    {
        $handler = new static();

        $handler->config = $config;
        $handler->api = Api::new(
            $handler->config
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


    private function handleResponse(Response $response, RawResponseDto $api_response) : void
    {
        $response->status($api_response->getStatus());

        foreach ($api_response->getHeaders() as $key => $value) {
            $response->header($key, $value);
        }

        foreach ($api_response->getCookies() as $key => $value) {
            $response->cookie($key, $value);
        }

        if ($api_response->getBody() !== null) {
            $response->write($api_response->getBody()->getBody());
        }

        if ($api_response->getSendfile() !== null) {
            $response->sendfile($api_response->getSendfile());
        }

        $response->end();
    }


    private function parseRequest(Request $request) : RawRequestDto
    {
        return RawRequestDto::new(
            $request->server["request_uri"],
            $request->getMethod(),
            $request->get,
            $request->getContent(),
            $request->header,
            $request->cookie
        );
    }
}
