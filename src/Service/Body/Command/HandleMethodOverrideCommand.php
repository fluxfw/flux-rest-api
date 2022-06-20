<?php

namespace FluxRestApi\Service\Body\Command;

use Exception;
use FluxRestApi\Adapter\Body\TextBodyDto;
use FluxRestApi\Adapter\Header\DefaultHeaderKey;
use FluxRestApi\Adapter\Method\CustomMethod;
use FluxRestApi\Adapter\Method\DefaultMethod;
use FluxRestApi\Adapter\Server\ServerRawRequestDto;
use FluxRestApi\Adapter\Server\ServerResponseDto;
use FluxRestApi\Adapter\ServerType\ServerType;
use FluxRestApi\Adapter\Status\DefaultStatus;
use Throwable;

class HandleMethodOverrideCommand
{

    private function __construct()
    {

    }


    public static function new() : static
    {
        return new static();
    }


    public function handleMethodOverride(ServerRawRequestDto $request) : ServerRawRequestDto|ServerResponseDto|null
    {
        $method_override = $request->getHeader(
            DefaultHeaderKey::X_HTTP_METHOD_OVERRIDE
        );

        if ($method_override === null) {
            return null;
        }

        try {
            if ($request->server_type !== ServerType::NGINX) {
                throw new Exception("Method overriding not enabled/needed for server " . $request->server_type->value);
            }

            $method_override = CustomMethod::factory(
                $method_override
            );

            if ($request->method !== DefaultMethod::POST) {
                throw new Exception("Method overriding only for " . DefaultMethod::POST->value);
            }

            if (!in_array($method_override, [DefaultMethod::DELETE, DefaultMethod::PATCH, DefaultMethod::PUT])) {
                throw new Exception("Method overriding with " . $method_override->value . " not supported");
            }

            return ServerRawRequestDto::new(
                $request->route,
                $request->original_route,
                $method_override,
                $request->server_type,
                $request->query_params,
                $request->body,
                $request->post,
                $request->files,
                array_filter($request->headers, fn(string $key) : bool => $key !== DefaultHeaderKey::X_HTTP_METHOD_OVERRIDE->value, ARRAY_FILTER_USE_KEY),
                $request->cookies
            );
        } catch (Throwable $ex) {
            file_put_contents("php://stdout", $ex);

            return ServerResponseDto::new(
                TextBodyDto::new(
                    "Invalid method"
                ),
                DefaultStatus::_405
            );
        }
    }
}
