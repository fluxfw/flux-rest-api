<?php

namespace FluxRestApi\Adapter\Handler;

use FluxRestApi\Adapter\Api\RestApi;
use FluxRestApi\Authorization\Authorization;
use FluxRestApi\Collector\RouteCollector;
use FluxRestApi\Libs\FluxRestBaseApi\Method\CustomMethod;
use FluxRestApi\Request\RawRequestDto;
use FluxRestApi\Response\ResponseDto;
use FluxRestApi\Server\LegacyDefaultServer;
use Swoole\Http\Request;
use Swoole\Http\Response;

class SwooleHandler
{

    private RestApi $rest_api;


    private function __construct(
        /*private readonly*/ RestApi $rest_api
    ) {
        $this->rest_api = $rest_api;
    }


    public static function new(
        RouteCollector $route_collector,
        ?Authorization $authorization = null
    ) : /*static*/ self
    {
        return new static(
            RestApi::new(
                $route_collector,
                $authorization
            )
        );
    }


    public function handle(Request $request, Response $response) : void
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
