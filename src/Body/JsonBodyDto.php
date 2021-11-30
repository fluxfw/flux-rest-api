<?php

namespace FluxRestApi\Body;

use FluxRestBaseApi\Body\BodyType;
use FluxRestBaseApi\Body\LegacyDefaultBodyType;

class JsonBodyDto implements BodyDto
{

    private $data;


    public static function new(/*mixed*/ $data) : /*static*/ self
    {
        $dto = new static();

        $dto->data = $data;

        return $dto;
    }


    public function getData()/* : mixed*/
    {
        return $this->data;
    }


    public function getType() : BodyType
    {
        return LegacyDefaultBodyType::JSON();
    }
}
