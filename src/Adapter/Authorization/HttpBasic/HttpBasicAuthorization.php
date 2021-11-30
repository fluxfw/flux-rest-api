<?php

namespace FluxRestApi\Adapter\Authorization\HttpBasic;

use Exception;
use FluxRestApi\Body\TextBodyDto;
use FluxRestApi\Request\RawRequestDto;
use FluxRestApi\Response\ResponseDto;
use FluxRestBaseApi\Header\LegacyDefaultHeader;
use FluxRestBaseApi\Status\LegacyDefaultStatus;

trait HttpBasicAuthorization
{

    private function parseHttpBasicAuthorization(RawRequestDto $request)/* : HttpBasicAuthorizationDto|ResponseDto*/
    {
        $authorization = $request->getHeader(
            LegacyDefaultHeader::AUTHORIZATION()->value
        );
        if (empty($authorization)) {
            return ResponseDto::new(
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
            throw new Exception("No " . HttpBasic::BASIC_AUTHORIZATION . " authorization");
        }

        $authorization = substr($authorization, strlen(HttpBasic::BASIC_AUTHORIZATION) + 1);

        $authorization = base64_decode($authorization);

        if (empty($authorization) || !str_contains($authorization, HttpBasic::SPLIT_USER_PASSWORD)) {
            throw new Exception("Missing user and password");
        }

        $password = explode(HttpBasic::SPLIT_USER_PASSWORD, $authorization);
        $user = array_shift($password);
        $password = implode(HttpBasic::SPLIT_USER_PASSWORD, $password);

        if (empty($user) || empty($password)) {
            throw new Exception("Missing user or password");
        }

        return HttpBasicAuthorizationDto::new(
            $user,
            $password
        );
    }
}