<?php

namespace FluxRestApi\Adapter\Authorization\HttpBasic;

class HttpBasicAuthorizationDto
{

    private string $password;
    private string $user;


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


    public function getPassword() : string
    {
        return $this->password;
    }


    public function getUser() : string
    {
        return $this->user;
    }
}
