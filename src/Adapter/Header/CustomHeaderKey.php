<?php

namespace FluxRestApi\Adapter\Header;

use JsonSerializable;
use LogicException;

class CustomHeaderKey implements HeaderKey, JsonSerializable
{

    private string $_value;


    private function __construct(
        /*public readonly*/ string $value
    ) {
        $this->_value = $value;
    }


    public static function factory(string $value) : HeaderKey
    {
        $value = strtolower($value);

        if (PHP_VERSION_ID >= 80100) {
            $key = DefaultHeaderKey::tryFrom($value);
        } else {
            $key = LegacyDefaultHeaderKey::tryFrom($value);
        }

        return $key ?? static::new(
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


    public final function __set(string $key, /*mixed*/ $value) : void
    {
        throw new LogicException("Can't set");
    }


    public function jsonSerialize() : string
    {
        return $this->value;
    }
}
