<?php

namespace FluxRestApi\Body;

use FluxRestBaseApi\Body\BodyType;
use FluxRestBaseApi\Body\LegacyDefaultBodyType;

class HtmlBodyDto implements BodyDto
{

    private string $html;


    public static function new(string $html) : /*static*/ self
    {
        $dto = new static();

        $dto->html = $html;

        return $dto;
    }


    public function getHtml() : string
    {
        return $this->html;
    }


    public function getType() : BodyType
    {
        return LegacyDefaultBodyType::HTML();
    }
}
