<?php

namespace FluxRestApi\Adapter\Authorization\ParseHttpBasic;

class HttpBasicAuthorizationDto
{

    public readonly string $password;
    public readonly string $user;


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
    ) : static {
        return new static(
            $user,
            $password
        );
    }
}
