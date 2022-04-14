<?php

namespace FluxRestApi\Channel\Body\Command;

use Exception;
use FluxRestApi\Adapter\Body\TextBodyDto;
use FluxRestApi\Adapter\Header\LegacyDefaultHeader;
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
            LegacyDefaultHeader::X_HTTP_METHOD_OVERRIDE()->value
        );

        if ($method_override === null) {
            return null;
        }

        try {
            if ($request->getServerType()->value !== LegacyDefaultServerType::NGINX()->value) {
                throw new Exception("Method overriding not enabled/needed for server " . $request->getServerType()->value);
            }

            $method_override = CustomMethod::factory($method_override);

            if ($request->getMethod()->value !== LegacyDefaultMethod::POST()->value) {
                throw new Exception("Method overriding only for " . LegacyDefaultMethod::POST()->value);
            }

            if (!in_array($method_override->value, [LegacyDefaultMethod::DELETE()->value, LegacyDefaultMethod::PATCH()->value, LegacyDefaultMethod::PUT()->value])) {
                throw new Exception("Method overriding with " . $method_override->value . " not supported");
            }

            return ServerRawRequestDto::new(
                $request->getRoute(),
                $method_override,
                $request->getServerType(),
                $request->getQueryParams(),
                $request->getBody(),
                $request->getPost(),
                $request->getFiles(),
                array_filter($request->getHeaders(), fn(string $header) : bool => $header !== LegacyDefaultHeader::X_HTTP_METHOD_OVERRIDE()->value, ARRAY_FILTER_USE_KEY),
                $request->getCookies()
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
