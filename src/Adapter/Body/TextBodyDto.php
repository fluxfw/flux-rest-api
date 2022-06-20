<?php

namespace FluxRestApi\Adapter\Body;

use FluxRestApi\Adapter\Body\Type\BodyType;
use FluxRestApi\Adapter\Body\Type\DefaultBodyType;

class TextBodyDto implements BodyDto
{

    private function __construct(
        public readonly string $text
    ) {

    }


    public static function new(
        ?string $text = null
    ) : static {
        return new static(
            $text ?? null
        );
    }


    public function getType() : BodyType
    {
        return DefaultBodyType::TEXT;
    }
}
