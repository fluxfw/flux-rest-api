<?php

namespace Fluxlabs\FluxRestApi\Body\Json;

use Exception;
use Fluxlabs\FluxRestApi\Body\BodyDto;
use Fluxlabs\FluxRestApi\Body\BodyParser;
use Fluxlabs\FluxRestApi\Body\Raw\RawBodyDto;

class JsonBodyParser implements BodyParser
{

    public static function getType() : string
    {
        return "application/json";
    }


    public static function new() : /*static*/ self
    {
        $parser = new static();

        return $parser;
    }


    public function parse(RawBodyDto $body) : BodyDto
    {
        $data = json_decode($body->getBody());

        if ($data === null) {
            throw new Exception("Invalid json body: " . json_last_error_msg());
        }

        return JsonBodyDto::new(
            $data
        );
    }


    public function toRaw(BodyDto $body) : RawBodyDto
    {
        $json = json_encode($body->getData(), JSON_UNESCAPED_SLASHES);

        if ($json === false) {
            throw new Exception("Invalid json body: " . json_last_error_msg());
        }

        return RawBodyDto::new(
            static::getType(),
            $json
        );
    }
}
