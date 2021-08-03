<?php

namespace Fluxlabs\FluxRestApi\Body\Text;

use Fluxlabs\FluxRestApi\Body\BodyDto;

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
}
