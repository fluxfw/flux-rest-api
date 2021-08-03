<?php

namespace Fluxlabs\FluxRestApi\Authorization\HttpBasicAuthorization;

use Exception;
use Fluxlabs\FluxRestApi\Request\RawRequestDto;

trait HttpBasicAuthorization
{

    public function get401Headers() : array
    {
        return [
            "WWW-Authenticate" => "Basic"
        ];
    }


    private function parseHttpBasicAuthorization(RawRequestDto $request) : HttpBasicAuthorizationDto
    {
        $authorization = $request->getHeader(
            "Authorization"
        );

        if (empty($authorization) || !str_starts_with($authorization, "Basic ")) {
            throw new Exception("Missing authorization");
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
