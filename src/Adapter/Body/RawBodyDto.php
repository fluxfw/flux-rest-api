<?php

namespace FluxRestApi\Adapter\Body;

class RawBodyDto
{

    private string $body;
    private string $type;


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


    public function getBody() : string
    {
        return $this->body;
    }


    public function getType() : string
    {
        return $this->type;
    }
}
