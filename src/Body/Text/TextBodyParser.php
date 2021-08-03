<?php

namespace Fluxlabs\FluxRestApi\Body\Text;

use Fluxlabs\FluxRestApi\Body\BodyDto;
use Fluxlabs\FluxRestApi\Body\BodyParser;
use Fluxlabs\FluxRestApi\Body\Raw\RawBodyDto;

class TextBodyParser implements BodyParser
{

    public static function getType() : string
    {
        return "text/plain";
    }


    public static function new() : /*static*/ self
    {
        $parser = new static();

        return $parser;
    }


    public function parse(RawBodyDto $body) : BodyDto
    {
        return TextBodyDto::new(
            $body->getBody()
        );
    }


    public function toRaw(BodyDto $body) : RawBodyDto
    {
        return RawBodyDto::new(
            static::getType(),
            $body->getText()
        );
    }
}
