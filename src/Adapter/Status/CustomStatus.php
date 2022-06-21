<?php

namespace FluxRestApi\Adapter\Status;

use JsonSerializable;
use LogicException;

class CustomStatus implements Status, JsonSerializable
{

    /**
     * @var static[]
     */
    private static array $cases;


    private function __construct(
        private readonly int $_value
    ) {

    }


    public static function factory(int $value) : Status
    {
        return DefaultStatus::tryFrom($value) ?? static::new(
                $value
            );
    }


    private static function new(
        int $value
    ) : static {
        static::$cases ??= [];

        return (static::$cases[$value] ??= new static(
            $value
        ));
    }


    public function __debugInfo() : ?array
    {
        return [
            "value" => $this->value
        ];
    }


    public final function __get(string $key) : int
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


    public function jsonSerialize() : int
    {
        return $this->value;
    }
}
