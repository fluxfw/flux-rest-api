<?php

namespace FluxRestApi\Adapter\Authorization\Schema;

use JsonSerializable;
use LogicException;

class CustomAuthorizationSchema implements AuthorizationSchema, JsonSerializable
{

    private function __construct(
        private readonly string $_value
    ) {

    }


    public static function factory(string $value) : AuthorizationSchema
    {
        return DefaultAuthorizationSchema::tryFrom($value) ?? static::new(
                $value
            );
    }


    private static function new(
        string $value
    ) : static {
        return new static(
            $value
        );
    }


    public function __debugInfo() : ?array
    {
        return [
            "value" => $this->value
        ];
    }


    public final function __get(string $key) : string
    {
        switch ($key) {
            case "value":
                return $this->_value;

            default:
                throw new LogicException("Can't get " . $key);
        }
    }


    public final function __set(string $key, mixed $value) : void
    {
        throw new LogicException("Can't set");
    }


    public function jsonSerialize() : string
    {
        return $this->value;
    }
}
