<?php

namespace FluxRestApi\Adapter\Route\Documentation;

use FluxRestApi\Adapter\Body\Type\BodyType;

class RouteContentTypeDocumentationDto
{

    public ?BodyType $content_type;
    public string $description;
    public string $type;


    private function __construct(
        /*public readonly*/ ?BodyType $content_type,
        /*public readonly*/ string $type,
        /*public readonly*/ string $description
    ) {
        $this->content_type = $content_type;
        $this->type = $type;
        $this->description = $description;
    }


    public static function new(
        ?BodyType $content_type = null,
        ?string $type = null,
        ?string $description = null
    ) : static {
        return new static(
            $content_type,
            $type ?? "",
            $description ?? ""
        );
    }
}
