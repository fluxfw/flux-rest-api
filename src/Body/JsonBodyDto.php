<?php

namespace Fluxlabs\FluxRestApi\Body;

class JsonBodyDto implements BodyDto
{

    private $data;


    public static function new($data) : /*static*/ self
    {
        $dto = new static();

        $dto->data = $data;

        return $dto;
    }


    public function getData()
    {
        return $this->data;
    }


    public function getType() : string
    {
        return BodyType::JSON;
    }
}
