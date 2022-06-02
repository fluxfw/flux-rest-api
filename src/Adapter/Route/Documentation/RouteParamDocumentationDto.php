<?php

namespace FluxRestApi\Adapter\Route\Documentation;

use JsonSerializable;

class RouteParamDocumentationDto implements JsonSerializable
{

    private string $description;
    private string $name;
    private string $type;


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
    ) : /*static*/ self
    {
        return new static(
            $name,
            $type ?? "",
            $description ?? ""
        );
    }


    public function getDescription() : string
    {
        return $this->description;
    }


    public function getName() : string
    {
        return $this->name;
    }


    public function getType() : string
    {
        return $this->type;
    }


    public function jsonSerialize() : object
    {
        return (object) [
            "description" => $this->description,
            "name"        => $this->name,
            "type"        => $this->type
        ];
    }
}
