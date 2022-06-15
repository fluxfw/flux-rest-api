<?php

namespace FluxRestApi\Adapter\Client;

use FluxRestApi\Adapter\Header\HeaderKey;
use FluxRestApi\Adapter\Status\LegacyDefaultStatus;
use FluxRestApi\Adapter\Status\Status;

class ClientResponseDto
{

    public ?string $body;
    /**
     * @var string[]
     */
    public array $headers;
    public Status $status;


    /**
     * @param string[] $headers
     */
    private function __construct(
        /*public readonly*/ Status $status,
        /*public readonly*/ array $headers,
        /*public readonly*/ ?string $body
    ) {
        $this->status = $status;
        $this->headers = $headers;
        $this->body = $body;
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
            $status ?? LegacyDefaultStatus::_200(),
            array_combine(array_map("strtolower", array_keys($headers)), $headers),
            $body
        );
    }


    public function getHeader(HeaderKey $key) : ?string
    {
        return $this->headers[strtolower($key->value)] ?? null;
    }
}
