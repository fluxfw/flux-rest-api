<?php

namespace Fluxlabs\FluxRestApi\Adapter\Authorization\HttpBasic;

use Exception;
use Fluxlabs\FluxRestApi\Body\TextBodyDto;
use Fluxlabs\FluxRestApi\Header\Header;
use Fluxlabs\FluxRestApi\Request\RawRequestDto;
use Fluxlabs\FluxRestApi\Response\ResponseDto;
use Fluxlabs\FluxRestApi\Status\Status;

trait HttpBasicAuthorization
{

    private function parseHttpBasicAuthorization(RawRequestDto $request)/* : HttpBasicAuthorizationDto|ResponseDto*/
    {
        $authorization = $request->getHeader(
            Header::AUTHORIZATION
        );
        if (empty($authorization)) {
            return ResponseDto::new(
                TextBodyDto::new(
                    "Authorization needed"
                ),
                Status::_401,
                [
                    Header::WWW_AUTHENTICATE => "Basic"
                ]
            );
        }

        if (!str_starts_with($authorization, "Basic ")) {
            throw new Exception("No basic authorization");
        }

        $authorization = substr($authorization, 6);

        $authorization = base64_decode($authorization);

        if (empty($authorization) || !str_contains($authorization, ":")) {
            throw new Exception("Missing user and password");
        }

        $password = explode(":", $authorization);
        $user = array_shift($password);
        $password = implode(":", $password);

        if (empty($user) || empty($password)) {
            throw new Exception("Missing user or password");
        }

        return HttpBasicAuthorizationDto::new(
            $user,
            $password
        );
    }
}
