<?php

namespace FluxRestApi\Adapter\Authorization\ParseHttpBasic;

use FluxRestApi\Adapter\Authorization\ParseHttp\HttpAuthorizationDto;
use FluxRestApi\Adapter\Authorization\ParseHttp\ParseHttpAuthorization;
use FluxRestApi\Adapter\Authorization\Schema\LegacyDefaultAuthorizationSchema;
use FluxRestApi\Adapter\Body\TextBodyDto;
use FluxRestApi\Adapter\Server\ServerRawRequestDto;
use FluxRestApi\Adapter\Server\ServerResponseDto;
use FluxRestApi\Adapter\Status\LegacyDefaultStatus;

trait ParseHttpBasicAuthorization
{

    use ParseHttpAuthorization;

    /**
     * @return HttpBasicAuthorizationDto|ServerResponseDto
     */
    private function parseHttpBasicAuthorization(ServerRawRequestDto $request)/* : HttpBasicAuthorizationDto|ServerResponseDto*/
    {
        $authorization = $this->parseHttpAuthorization(
            $request,
            HttpAuthorizationDto::new(
                LegacyDefaultAuthorizationSchema::BASIC()
            )
        );
        if ($authorization instanceof ServerResponseDto) {
            return $authorization;
        }

        if ($authorization->getSchema()->value !== LegacyDefaultAuthorizationSchema::BASIC()->value) {
            return ServerResponseDto::new(
                TextBodyDto::new(
                    LegacyDefaultAuthorizationSchema::BASIC()->value . " authorization schema needed"
                ),
                LegacyDefaultStatus::_400()
            );
        }

        $authorization = base64_decode($authorization->getParameters());

        if ($authorization === false) {
            return ServerResponseDto::new(
                TextBodyDto::new(
                    "Invalid " . LegacyDefaultAuthorizationSchema::BASIC()->value . " authorization"
                ),
                LegacyDefaultStatus::_400()
            );
        }

        if (empty($authorization) || !str_contains($authorization, ParseHttpBasicAuthorization_::SPLIT_USER_PASSWORD)) {
            return ServerResponseDto::new(
                TextBodyDto::new(
                    "Missing authorization user or password"
                ),
                LegacyDefaultStatus::_400()
            );
        }

        $password = explode(ParseHttpBasicAuthorization_::SPLIT_USER_PASSWORD, $authorization);
        $user = array_shift($password);
        $password = implode(ParseHttpBasicAuthorization_::SPLIT_USER_PASSWORD, $password);

        if (empty($user) || empty($password)) {
            return ServerResponseDto::new(
                TextBodyDto::new(
                    "Missing authorization user or password"
                ),
                LegacyDefaultStatus::_400()
            );
        }

        return HttpBasicAuthorizationDto::new(
            $user,
            $password
        );
    }
}
