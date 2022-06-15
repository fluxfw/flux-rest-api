<?php

namespace FluxRestApi\Adapter\Route\Documentation;

use FluxRestApi\Adapter\Body\Type\BodyType;
use FluxRestApi\Adapter\Status\LegacyDefaultStatus;
use FluxRestApi\Adapter\Status\Status;

class RouteResponseDocumentationDto
{

    public ?BodyType $content_type;
    public string $description;
    public Status $status;
    public string $type;


    private function __construct(
        /*public readonly*/ ?BodyType $content_type,
        /*public readonly*/ Status $status,
        /*public readonly*/ string $type,
        /*public readonly*/ string $description
    ) {
        $this->content_type = $content_type;
        $this->status = $status;
        $this->type = $type;
        $this->description = $description;
    }


    public static function new(
        ?BodyType $content_type = null,
        ?Status $status = null,
        ?string $type = null,
        ?string $description = null
    ) : static {
        return new static(
            $content_type,
            $status ?? LegacyDefaultStatus::_200(),
            $type ?? "",
            $description ?? ""
        );
    }
}
