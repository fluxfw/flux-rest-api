<?php

namespace FluxRestApi\Adapter\Authorization\HttpBasic;

use FluxRestApi\Adapter\Body\TextBodyDto;
use FluxRestApi\Adapter\Header\LegacyDefaultHeader;
use FluxRestApi\Adapter\Server\ServerRawRequestDto;
use FluxRestApi\Adapter\Server\ServerResponseDto;
use FluxRestApi\Adapter\Status\LegacyDefaultStatus;

trait HttpBasicAuthorization
{

    private function parseHttpBasicAuthorization(ServerRawRequestDto $request)/* : HttpBasicAuthorizationDto|ServerResponseDto*/
    {
        $authorization = $request->getHeader(
            LegacyDefaultHeader::AUTHORIZATION()->value
        );
        if (empty($authorization)) {
            return ServerResponseDto::new(
                TextBodyDto::new(
                    "Authorization needed"
                ),
                LegacyDefaultStatus::_401(),
                [
                    LegacyDefaultHeader::WWW_AUTHENTICATE()->value => HttpBasic::BASIC_AUTHORIZATION
                ]
            );
        }

        if (!str_starts_with($authorization, HttpBasic::BASIC_AUTHORIZATION . " ")) {
            return ServerResponseDto::new(
                TextBodyDto::new(
                    "Invalid authorization type"
                ),
                LegacyDefaultStatus::_400()
            );
        }

        $authorization = substr($authorization, strlen(HttpBasic::BASIC_AUTHORIZATION) + 1);

        $authorization = base64_decode($authorization);

        if (empty($authorization) || !str_contains($authorization, HttpBasic::SPLIT_USER_PASSWORD)) {
            return ServerResponseDto::new(
                TextBodyDto::new(
                    "Missing user or password"
                ),
                LegacyDefaultStatus::_400()
            );
        }

        $password = explode(HttpBasic::SPLIT_USER_PASSWORD, $authorization);
        $user = array_shift($password);
        $password = implode(HttpBasic::SPLIT_USER_PASSWORD, $password);

        if (empty($user) || empty($password)) {
            return ServerResponseDto::new(
                TextBodyDto::new(
                    "Missing user or password"
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
