<?php

namespace FluxRestApi\Adapter\Body;

use FluxRestApi\Adapter\Body\Type\BodyType;
use FluxRestApi\Adapter\Body\Type\LegacyDefaultBodyType;

class JsonBodyDto implements BodyDto
{

    /**
     * @var mixed
     */
    public $data;


    private function __construct(
        /*public readonly mixed*/ $data
    ) {
        $this->data = $data;
    }


    public static function new(
        /*mixed*/ $data
    ) : static {
        return new static(
            $data
        );
    }


    public function getType() : BodyType
    {
        return LegacyDefaultBodyType::JSON();
    }
}
