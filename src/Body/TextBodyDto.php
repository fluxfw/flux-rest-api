<?php

namespace FluxRestApi\Body;

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


    public function getType() : string
    {
        return BodyType::TEXT;
    }
}
