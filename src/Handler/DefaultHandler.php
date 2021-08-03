<?php

namespace Fluxlabs\FluxRestApi\Handler;

use Fluxlabs\FluxRestApi\Api\Api;
use Fluxlabs\FluxRestApi\Config\Config;
use Fluxlabs\FluxRestApi\Log\LogHelper;
use Fluxlabs\FluxRestApi\Request\RawRequestDto;
use Fluxlabs\FluxRestApi\Response\RawResponseDto;

class DefaultHandler
{

    use LogHelper;

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


    public function handle() : void
    {
        $this->handleResponse(
            $this->api->handleRequest(
                $this->parseRequest()
            )
        );
    }


    private function handleResponse(RawResponseDto $response) : void
    {
        http_response_code($response->getStatus());

        $headers = $response->getHeaders();

        if ($response->getSendfile() !== null) {
            $headers["X-Accel-Redirect"] = $response->getSendfile();
        }

        foreach ($headers as $key => $value) {
            header(rawurlencode($key) . ": " . rawurlencode($value));
        }

        foreach ($response->getCookies() as $key => $value) {
            $_COOKIE[$key] = $value;
        }

        if ($response->getBody() !== null) {
            echo $response->getBody()->getBody();
        }
    }


    private function parseRequest() : RawRequestDto
    {
        $query_string = $_SERVER["QUERY_STRING"];

        if (str_contains($query_string, "&")) {
            $route_url = explode("&", $query_string)[0];
        } else {
            $route_url = $query_string;
        }

        $query = $_GET;
        unset($query[$route_url]);

        return RawRequestDto::new(
            $route_url,
            $_SERVER["REQUEST_METHOD"],
            $query,
            file_get_contents("php://input"),
            getallheaders(),
            $_COOKIE
        );
    }
}
