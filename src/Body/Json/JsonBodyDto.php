<?php

namespace Fluxlabs\FluxRestApi\Body\Json;

use Fluxlabs\FluxRestApi\Body\BodyDto;

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
}
