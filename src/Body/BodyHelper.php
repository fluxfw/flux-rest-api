<?php

namespace Fluxlabs\FluxRestApi\Body;

use Exception;
use Fluxlabs\FluxRestApi\Body\Raw\RawBodyDto;
use Throwable;

trait BodyHelper
{

    private function getBodyParser(string $body_class) : BodyParser
    {
        try {
            if (!class_exists($body_class) || !is_a($body_class, BodyDto::class, true)) {
                throw new Exception($body_class . " is no BodyDto");
            }

            $body_parser_class = substr($body_class, 0, -3) . "Parser";

            if (!class_exists($body_parser_class) || !is_a($body_parser_class, BodyParser::class, true) || !method_exists($body_parser_class, "new")) {
                throw new Exception($body_parser_class . " is no BodyParser");
            }

            return [$body_parser_class, "new"]();
        } catch (Throwable $ex) {
            throw new Exception("BodyParser of " . $body_class . " not found");
        }
    }


    private function mapHeaders(array $headers) : array
    {
        $headers_ = [];

        foreach ($headers as $key => $value) {
            $headers_[implode("-", array_map("ucfirst", explode("-", strtolower($key))))] = $value;
        }

        return $headers_;
    }


    private function parseBody(?string $type, ?string $body, ?array $body_classes = null) : ?BodyDto
    {
        if (empty($type) && empty($body)) {
            return null;
        }

        if (empty($type) && !empty($body)) {
            throw new Exception("Body without type set");
        }

        if ($body === null) {
            throw new Exception("Type without body set");
        }

        $body = RawBodyDto::new(
            $type,
            $body
        );

        if ($body_classes === null) {
            return $body;
        }

        foreach ($body_classes as $body_class) {
            if (is_a($body_class, RawBodyDto::class, true)) {
                return $body;
            }

            $body_parser = $this->getBodyParser(
                $body_class
            );

            if (str_contains($type, $body_parser::getType())) {
                return $body_parser->parse(
                    $body
                );
            }
        }

        throw new Exception("Unsupported body type " . $type);
    }


    private function toRawBody(?BodyDto $body) : ?RawBodyDto
    {
        if ($body === null) {
            return null;
        }

        if ($body instanceof RawBodyDto) {
            return $body;
        }

        return $this->getBodyParser(
            get_class($body)
        )
            ->toRaw(
                $body
            );
    }
}
