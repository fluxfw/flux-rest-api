<?php

namespace FluxRestApi\Adapter\Body;

class RawBodyDto
{

    public string $body;
    public string $type;


    private function __construct(
        /*public readonly*/ string $type,
        /*public readonly*/ string $body
    ) {
        $this->type = $type;
        $this->body = $body;
    }


    public static function new(
        ?string $type = null,
        ?string $body = null
    ) : /*static*/ self
    {
        return new static(
            $type ?? "",
            $body ?? ""
        );
    }
}
