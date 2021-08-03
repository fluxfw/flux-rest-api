<?php

namespace Fluxlabs\FluxRestApi\Body\Raw;

use Fluxlabs\FluxRestApi\Body\BodyDto;

class RawBodyDto implements BodyDto
{

    private string $body;
    private string $type;


    public static function new(string $type, string $body) : /*static*/ self
    {
        $dto = new static();

        $dto->type = $type;
        $dto->body = $body;

        return $dto;
    }


    public function getBody() : string
    {
        return $this->body;
    }


    public function getType() : string
    {
        return $this->type;
    }
}
