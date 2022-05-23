<?php

namespace FluxRestApi\Adapter\Authorization\ParseHttp;

use FluxRestApi\Adapter\Authorization\Schema\AuthorizationSchema;

class HttpAuthorizationDto
{

    private string $parameters;
    private AuthorizationSchema $schema;


    private function __construct(
        /*public readonly*/ AuthorizationSchema $schema,
        /*public readonly*/ string $parameters
    ) {
        $this->schema = $schema;
        $this->parameters = $parameters;
    }


    public static function new(
        AuthorizationSchema $schema,
        ?string $parameters = null
    ) : /*static*/ self
    {
        return new static(
            $schema,
            $parameters ?? ""
        );
    }


    public function getParameters() : string
    {
        return $this->parameters;
    }


    public function getSchema() : AuthorizationSchema
    {
        return $this->schema;
    }
}
