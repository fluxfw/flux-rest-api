<?php

namespace Fluxlabs\FluxRestApi\Body;

class BodyDto
{

    private $body;
    private string $type;


    public static function new(string $type, $body) : /*static*/ self
    {
        $dto = new static();

        $dto->type = $type;
        $dto->body = $body;

        return $dto;
    }


    public function getBody()
    {
        return $this->body;
    }


    public function getType() : string
    {
        return $this->type;
    }
}
