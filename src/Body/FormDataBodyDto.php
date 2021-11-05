<?php

namespace FluxRestApi\Body;

use FluxRestBaseApi\Body\BodyType;

class FormDataBodyDto implements BodyDto
{

    private array $data;
    private array $files;


    public static function new(array $data, array $files) : /*static*/ self
    {
        $dto = new static();

        $dto->data = $data;
        $dto->files = $files;

        return $dto;
    }


    public function getData() : array
    {
        return $this->data;
    }


    public function getFiles() : array
    {
        return $this->files;
    }


    public function getType() : string
    {
        return BodyType::FORM_DATA;
    }
}
