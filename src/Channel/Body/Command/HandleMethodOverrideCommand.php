<?php

namespace FluxRestApi\Service\Body\Command;

use Exception;
use FluxRestApi\Adapter\Body\TextBodyDto;
use FluxRestApi\Adapter\Header\LegacyDefaultHeaderKey;
use FluxRestApi\Adapter\Method\CustomMethod;
use FluxRestApi\Adapter\Method\LegacyDefaultMethod;
use FluxRestApi\Adapter\Server\ServerRawRequestDto;
use FluxRestApi\Adapter\Server\ServerResponseDto;
use FluxRestApi\Adapter\ServerType\LegacyDefaultServerType;
use FluxRestApi\Adapter\Status\LegacyDefaultStatus;
use Throwable;

class HandleMethodOverrideCommand
{

    private function __construct()
    {

    }


    public static function new() : /*static*/ self
    {
        return new static();
    }


    /**
     * @return ServerRawRequestDto|ServerResponseDto|null
     */
    public function handleMethodOverride(ServerRawRequestDto $request)/* : ServerRawRequestDto|ServerResponseDto|null*/
    {
        $method_override = $request->getHeader(
            LegacyDefaultHeaderKey::X_HTTP_METHOD_OVERRIDE()
        );

        if ($method_override === null) {
            return null;
        }

        try {
            if ($request->server_type->value !== LegacyDefaultServerType::NGINX()->value) {
                throw new Exception("Method overriding not enabled/needed for server " . $request->server_type->value);
            }

            $method_override = CustomMethod::factory(
                $method_override
            );

            if ($request->method->value !== LegacyDefaultMethod::POST()->value) {
                throw new Exception("Method overriding only for " . LegacyDefaultMethod::POST()->value);
            }

            if (!in_array($method_override->value, [LegacyDefaultMethod::DELETE()->value, LegacyDefaultMethod::PATCH()->value, LegacyDefaultMethod::PUT()->value])) {
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
                array_filter($request->headers, fn(string $key) : bool => $key !== LegacyDefaultHeaderKey::X_HTTP_METHOD_OVERRIDE()->value, ARRAY_FILTER_USE_KEY),
                $request->cookies
            );
        } catch (Throwable $ex) {
            file_put_contents("php://stdout", $ex);

            return ServerResponseDto::new(
                TextBodyDto::new(
                    "Invalid method"
                ),
                LegacyDefaultStatus::_405()
            );
        }
    }
}
