<?php

namespace FluxRestApi\Body;

class JsonBodyDto implements BodyDto
{

    //private mixed $data;
    private $data;


    public static function new(/*mixed*/ $data) : /*static*/ self
    {
        $dto = new static();

        $dto->data = $data;

        return $dto;
    }


    public function getData()/* : mixed*/
    {
        return $this->data;
    }


    public function getType() : string
    {
        return BodyType::JSON;
    }
}
