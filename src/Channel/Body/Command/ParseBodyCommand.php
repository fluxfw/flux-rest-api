<?php

namespace FluxRestApi\Channel\Body\Command;

use Exception;
use FluxRestApi\Adapter\Body\BodyDto;
use FluxRestApi\Adapter\Body\FormDataBodyDto;
use FluxRestApi\Adapter\Body\HtmlBodyDto;
use FluxRestApi\Adapter\Body\JsonBodyDto;
use FluxRestApi\Adapter\Body\RawBodyDto;
use FluxRestApi\Adapter\Body\TextBodyDto;
use FluxRestApi\Adapter\Body\Type\LegacyDefaultBodyType;

class ParseBodyCommand
{

    private function __construct()
    {

    }


    public static function new() : /*static*/ self
    {
        return new static();
    }


    public function parseBody(RawBodyDto $body, ?array $post = null, ?array $files = null) : ?BodyDto
    {
        if (empty($body->getType())) {
            return null;
        }

        switch (true) {
            case str_contains($body->getType(), LegacyDefaultBodyType::FORM_DATA()->value):
            case str_contains($body->getType(), LegacyDefaultBodyType::FORM_DATA_2()->value):
                return FormDataBodyDto::new(
                    $post,
                    $files
                );

            case str_contains($body->getType(), LegacyDefaultBodyType::HTML()->value):
                return HtmlBodyDto::new(
                    $body->getBody()
                );

            case str_contains($body->getType(), LegacyDefaultBodyType::JSON()->value):
                $data = json_decode($body->getBody());

                $error_code = json_last_error();
                if ($error_code !== JSON_ERROR_NONE) {
                    throw new Exception(json_last_error_msg(), $error_code);
                }

                return JsonBodyDto::new(
                    $data
                );

            case str_contains($body->getType(), LegacyDefaultBodyType::TEXT()->value):
                return TextBodyDto::new(
                    $body->getBody()
                );

            default:
                return null;
        }
    }
}
