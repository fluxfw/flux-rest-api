<?php

namespace FluxRestApi\Adapter\Server;

use FluxRestApi\Adapter\Body\BodyDto;
use FluxRestApi\Adapter\Cookie\CookieDto;
use FluxRestApi\Adapter\Header\HeaderKey;
use FluxRestApi\Adapter\Status\DefaultStatus;
use FluxRestApi\Adapter\Status\Status;

class ServerResponseDto
{

    /**
     * @param string[]    $headers
     * @param CookieDto[] $cookies
     */
    private function __construct(
        public readonly ?BodyDto $body,
        public readonly Status $status,
        public readonly array $headers,
        public readonly array $cookies,
        public readonly ?string $sendfile,
        public readonly ?string $raw_body
    ) {

    }


    /**
     * @param string[]|null    $headers
     * @param CookieDto[]|null $cookies
     */
    public static function new(
        ?BodyDto $body = null,
        ?Status $status = null,
        ?array $headers = null,
        ?array $cookies = null,
        ?string $sendfile = null,
        ?string $raw_body = null
    ) : static {
        $headers ??= [];

        return new static(
            $body,
            $status ?? DefaultStatus::_200,
            array_combine(array_map("strtolower", array_keys($headers)), $headers),
            $cookies ?? [],
            $sendfile,
            $raw_body
        );
    }


    public function getCookie(string $name) : ?CookieDto
    {
        return $this->cookies[$name] ?? null;
    }


    public function getHeader(HeaderKey $key) : ?string
    {
        return $this->headers[strtolower($key->value)] ?? null;
    }
}
