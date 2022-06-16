<?php

namespace FluxRestApi\Adapter\Authorization\ParseHttp;

use FluxRestApi\Adapter\Authorization\Schema\AuthorizationSchema;

class HttpAuthorizationDto
{

    private function __construct(
        public readonly AuthorizationSchema $schema,
        public readonly string $parameters
    ) {

    }


    public static function new(
        AuthorizationSchema $schema,
        ?string $parameters = null
    ) : static {
        return new static(
            $schema,
            $parameters ?? ""
        );
    }
}
