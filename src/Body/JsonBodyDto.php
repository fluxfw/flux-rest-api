<?php

namespace FluxRestApi\Body;

use FluxRestApi\Libs\FluxRestBaseApi\Body\BodyType;
use FluxRestApi\Libs\FluxRestBaseApi\Body\LegacyDefaultBodyType;

class JsonBodyDto implements BodyDto
{

    private $data;


    private function __construct(
        /*public readonly mixed*/ $data
    ) {
        $this->data = $data;
    }


    public static function new(
        /*mixed*/ $data
    ) : /*static*/ self
    {
        return new static(
            $data
        );
    }


    public function getData()/* : mixed*/
    {
        return $this->data;
    }


    public function getType() : BodyType
    {
        return LegacyDefaultBodyType::JSON();
    }
}
