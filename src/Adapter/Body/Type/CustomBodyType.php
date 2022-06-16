<?php

namespace FluxRestApi\Adapter\Body\Type;

use JsonSerializable;
use LogicException;

class CustomBodyType implements BodyType, JsonSerializable
{

    private function __construct(
        private readonly string $_value
    ) {

    }


    public static function factory(string $value) : BodyType
    {
        if (PHP_VERSION_ID >= 80100) {
            $body_type = DefaultBodyType::tryFrom($value);
        } else {
            $body_type = LegacyDefaultBodyType::tryFrom($value);
        }

        return $body_type ?? static::new(
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
