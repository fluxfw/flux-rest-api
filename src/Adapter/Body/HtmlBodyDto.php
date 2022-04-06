<?php

namespace FluxRestApi\Adapter\Body;

use FluxRestApi\Adapter\Body\Type\BodyType;
use FluxRestApi\Adapter\Body\Type\LegacyDefaultBodyType;

class HtmlBodyDto implements BodyDto
{

    private string $html;


    private function __construct(
        /*public readonly*/ string $html
    ) {
        $this->html = $html;
    }


    public static function new(
        ?string $html = null
    ) : /*static*/ self
    {
        return new static(
            $html ?? ""
        );
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
