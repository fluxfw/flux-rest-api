<?php

namespace FluxRestApi\Adapter\Client;

use FluxRestApi\Adapter\Status\LegacyDefaultStatus;
use FluxRestApi\Adapter\Status\Status;

class ClientResponseDto
{

    private ?string $body;
    /**
     * @var string[]
     */
    private array $headers;
    private Status $status;


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
    ) : /*static*/ self
    {
        $headers ??= [];

        return new static(
            $status ?? LegacyDefaultStatus::_200(),
            array_combine(array_map("strtolower", array_keys($headers)), $headers),
            $body
        );
    }


    public function getBody() : ?string
    {
        return $this->body;
    }


    public function getHeader(string $key) : ?string
    {
        return $this->headers[strtolower($key)] ?? null;
    }


    /**
     * @return string[]
     */
    public function getHeaders() : array
    {
        return $this->headers;
    }


    public function getStatus() : Status
    {
        return $this->status;
    }
}
