<?php

namespace FluxRestApi\Body;

use FluxRestApi\Libs\FluxRestBaseApi\Body\BodyType;
use FluxRestApi\Libs\FluxRestBaseApi\Body\LegacyDefaultBodyType;

class FormDataBodyDto implements BodyDto
{

    private array $data;
    private array $files;


    private function __construct(
        /*public readonly*/ array $data,
        /*public readonly*/ array $files
    ) {
        $this->data = $data;
        $this->files = $files;
    }


    public static function new(
        array $data,
        array $files
    ) : /*static*/ self
    {
        return new static(
            $data,
            $files
        );
    }


    public function getData() : array
    {
        return $this->data;
    }


    public function getFiles() : array
    {
        return $this->files;
    }


    public function getType() : BodyType
    {
        return LegacyDefaultBodyType::FORM_DATA();
    }
}
