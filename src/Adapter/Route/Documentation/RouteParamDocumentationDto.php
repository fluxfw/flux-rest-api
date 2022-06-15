<?php

namespace FluxRestApi\Adapter\Route\Documentation;

class RouteParamDocumentationDto
{

    public string $description;
    public string $name;
    public string $type;


    private function __construct(
        /*public readonly*/ string $name,
        /*public readonly*/ string $type,
        /*public readonly*/ string $description
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->description = $description;
    }


    public static function new(
        string $name,
        ?string $type = null,
        ?string $description = null
    ) : static {
        return new static(
            $name,
            $type ?? "",
            $description ?? ""
        );
    }
}
