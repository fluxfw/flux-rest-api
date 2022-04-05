<?php

namespace FluxRestApi\Body;

class TextBodyDto implements BodyDto
{

    private string $text;


    private function __construct(
        /*public readonly*/ string $text
    ) {
        $this->text = $text;
    }


    public static function new(
        string $text
    ) : /*static*/ self
    {
        return new static(
            $text
        );
    }


    public function getText() : string
    {
        return $this->text;
    }


    public function getType() : BodyType
    {
        return LegacyDefaultBodyType::TEXT();
    }
}
