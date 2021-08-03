<?php

namespace Fluxlabs\FluxRestApi\Body\Html;

use Fluxlabs\FluxRestApi\Body\BodyDto;

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
}
