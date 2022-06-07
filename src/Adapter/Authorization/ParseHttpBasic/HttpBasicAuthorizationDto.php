<?php

namespace FluxRestApi\Adapter\Authorization\ParseHttpBasic;

class HttpBasicAuthorizationDto
{

    public string $password;
    public string $user;


    private function __construct(
        /*public readonly*/ string $user,
        /*public readonly*/ string $password
    ) {
        $this->user = $user;
        $this->password = $password;
    }


    public static function new(
        string $user,
        string $password
    ) : /*static*/ self
    {
        return new static(
            $user,
            $password
        );
    }
}
