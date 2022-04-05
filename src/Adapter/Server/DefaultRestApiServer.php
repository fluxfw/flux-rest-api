<?php

namespace FluxRestApi\Adapter\Server;

use FluxRestApi\Adapter\Api\RestApi;
use FluxRestApi\Authorization\Authorization;
use FluxRestApi\Collector\RouteCollector;
use FluxRestApi\Header\LegacyDefaultHeader;
use FluxRestApi\Method\CustomMethod;
use FluxRestApi\Request\RawRequestDto;
use FluxRestApi\Response\ResponseDto;
use FluxRestApi\Server\LegacyDefaultServer;
use LogicException;

class DefaultRestApiServer
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


    public function handle() : void
    {
        $this->handleResponse(
            $this->rest_api->handleRequest(
                $request = $this->parseRequest()
            ),
            $request
        );
    }


    private function handleResponse(ResponseDto $response, RawRequestDto $request) : void
    {
        if (headers_sent($filename, $line)) {
            throw new LogicException("Do not manually output headers or body in " . $filename . ":" . $line);
        }

        http_response_code($response->getStatus()->value);

        $headers = $response->getHeaders();

        if ($response->getSendfile() !== null) {
            if ($request->getServer()->value === LegacyDefaultServer::NGINX()->value) {
                $headers[LegacyDefaultHeader::X_ACCEL_REDIRECT()->value] = $response->getSendfile();
            } else {
                $headers[LegacyDefaultHeader::X_SENDFILE()->value] = $response->getSendfile();
            }
            $headers[LegacyDefaultHeader::CONTENT_TYPE()->value] = "";
        }

        foreach ($headers as $key => $value) {
            header($key . ":" . $value);
        }

        foreach ($response->getCookies() as $cookie) {
            if ($cookie->getValue() !== null) {
                setcookie(
                    $cookie->getName(),
                    $cookie->getValue(),
                    $cookie->getExpiresIn() !== null ? (time() + $cookie->getExpiresIn()) : 0,
                    $cookie->getPath(),
                    $cookie->getDomain(),
                    $cookie->isSecure(),
                    $cookie->isHttpOnly()
                );
            } else {
                setcookie(
                    $cookie->getName(),
                    "",
                    0,
                    $cookie->getPath(),
                    $cookie->getDomain()
                );
            }
        }

        if ($response->getRawBody() !== null) {
            echo $response->getRawBody();
        }
    }


    private function parseRequest() : RawRequestDto
    {
        $route_url = explode("&", $_SERVER["QUERY_STRING"])[0];

        $query = $_GET;
        unset($query[$route_url]);

        return RawRequestDto::new(
            $route_url,
            CustomMethod::factory($_SERVER["REQUEST_METHOD"]),
            str_contains($_SERVER["SERVER_SOFTWARE"], "nginx") ? LegacyDefaultServer::NGINX() : LegacyDefaultServer::APACHE(),
            $query,
            file_get_contents("php://input") ?: null,
            $_POST,
            $_FILES,
            getallheaders(),
            $_COOKIE
        );
    }
}
