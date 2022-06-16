<?php

namespace FluxRestApi\Adapter\Authorization\ParseHttpBasic;

class HttpBasicAuthorizationDto
{

    private function __construct(
        public readonly string $user,
        public readonly string $password
    ) {

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
