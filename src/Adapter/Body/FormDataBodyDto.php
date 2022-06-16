<?php

namespace FluxRestApi\Adapter\Body;

use FluxRestApi\Adapter\Body\Type\BodyType;
use FluxRestApi\Adapter\Body\Type\LegacyDefaultBodyType;

class FormDataBodyDto implements BodyDto
{

    private function __construct(
        public readonly array $data,
        public readonly array $files
    ) {

    }


    public static function new(
        ?array $data = null,
        ?array $files = null
    ) : static {
        return new static(
            $data ?? [],
            $files ?? []
        );
    }


    public function getType() : BodyType
    {
        return LegacyDefaultBodyType::FORM_DATA();
    }
}
