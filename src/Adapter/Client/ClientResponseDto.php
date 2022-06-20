<?php

namespace FluxRestApi\Adapter\Client;

use FluxRestApi\Adapter\Header\HeaderKey;
use FluxRestApi\Adapter\Status\DefaultStatus;
use FluxRestApi\Adapter\Status\Status;

class ClientResponseDto
{

    /**
     * @param string[] $headers
     */
    private function __construct(
        public readonly Status $status,
        public readonly array $headers,
        public readonly ?string $body
    ) {

    }


    /**
     * @param string[]|null $headers
     */
    public static function new(
        ?Status $status = null,
        ?array $headers = null,
        ?string $body = null
    ) : static {
        $headers ??= [];

        return new static(
            $status ?? DefaultStatus::_200,
            array_combine(array_map("strtolower", array_keys($headers)), $headers),
            $body
        );
    }


    public function getHeader(HeaderKey $key) : ?string
    {
        return $this->headers[strtolower($key->value)] ?? null;
    }
}
