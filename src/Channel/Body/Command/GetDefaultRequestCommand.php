<?php

namespace FluxRestApi\Channel\Body\Command;

use FluxRestApi\Adapter\Method\CustomMethod;
use FluxRestApi\Adapter\Server\ServerRawRequestDto;
use FluxRestApi\Adapter\ServerType\LegacyDefaultServerType;

class GetDefaultRequestCommand
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
        $query_params = $_GET;

        if ($rest_api_server ?? false) {
            $route = explode("&", $_SERVER["QUERY_STRING"])[0];
            unset($query_params[$route]);
        } else {
            $route = explode("?", $_SERVER["REQUEST_URI"])[0];
        }

        return ServerRawRequestDto::new(
            $route,
            CustomMethod::factory($_SERVER["REQUEST_METHOD"]),
            str_contains($_SERVER["SERVER_SOFTWARE"], "nginx") ? LegacyDefaultServerType::NGINX() : LegacyDefaultServerType::APACHE(),
            $query_params,
            file_get_contents("php://input") ?: null,
            $_POST,
            $_FILES,
            getallheaders(),
            $_COOKIE
        );
    }
}
