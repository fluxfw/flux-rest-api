<?php

namespace FluxRestApi\Body;

use FluxRestBaseApi\Body\BodyType;
use FluxRestBaseApi\Body\LegacyDefaultBodyType;

class TextBodyDto implements BodyDto
{

    private string $text;


    public static function new(string $text) : /*static*/ self
    {
        $dto = new static();

        $dto->text = $text;

        return $dto;
    }


    public function getText() : string
    {
        return $this->text;
    }


    public function getType() : BodyType
    {
        return LegacyDefaultBodyType::TEXT();
    }
}
