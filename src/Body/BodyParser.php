<?php

namespace Fluxlabs\FluxRestApi\Body;

use Fluxlabs\FluxRestApi\Body\Raw\RawBodyDto;

interface BodyParser
{

    public static function getType() : string;


    public function parse(RawBodyDto $body) : BodyDto;


    public function toRaw(BodyDto $body) : RawBodyDto;
}
