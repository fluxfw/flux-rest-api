<?php

namespace FluxRestApi\Adapter\Route\Documentation;

use FluxRestApi\Adapter\Body\Type\BodyType;
use JsonSerializable;

class RouteContentTypeDocumentationDto implements JsonSerializable
{

    private ?BodyType $content_type;
    private string $description;
    private string $type;


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
    ) : /*static*/ self
    {
        return new static(
            $content_type,
            $type ?? "",
            $description ?? ""
        );
    }


    public function getContentType() : ?BodyType
    {
        return $this->content_type;
    }


    public function getDescription() : string
    {
        return $this->description;
    }


    public function getType() : string
    {
        return $this->type;
    }


    public function jsonSerialize() : object
    {
        return (object) [
            "content_type" => $this->content_type,
            "description"  => $this->description,
            "type"         => $this->type
        ];
    }
}
