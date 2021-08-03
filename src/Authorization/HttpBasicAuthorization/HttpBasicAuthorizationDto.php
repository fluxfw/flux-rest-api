<?php

namespace Fluxlabs\FluxRestApi\Authorization\HttpBasicAuthorization;

class HttpBasicAuthorizationDto
{

    private string $password;
    private string $user;


    public static function new(string $user, string $password) : /*static*/ self
    {
        $dto = new static();

        $dto->user = $user;
        $dto->password = $password;

        return $dto;
    }


    public function getPassword() : string
    {
        return $this->password;
    }


    public function getUser() : string
    {
        return $this->user;
    }
}
