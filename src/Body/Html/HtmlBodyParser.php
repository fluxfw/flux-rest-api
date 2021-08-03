<?php

namespace Fluxlabs\FluxRestApi\Body\Html;

use Fluxlabs\FluxRestApi\Body\BodyDto;
use Fluxlabs\FluxRestApi\Body\BodyParser;
use Fluxlabs\FluxRestApi\Body\Raw\RawBodyDto;

class HtmlBodyParser implements BodyParser
{

    public static function getType() : string
    {
        return "text/html";
    }


    public static function new() : /*static*/ self
    {
        $parser = new static();

        return $parser;
    }


    public function parse(RawBodyDto $body) : BodyDto
    {
        return HtmlBodyDto::new(
            $body->getBody()
        );
    }


    public function toRaw(BodyDto $body) : RawBodyDto
    {
        return RawBodyDto::new(
            static::getType(),
            $body->getHtml()
        );
    }
}
