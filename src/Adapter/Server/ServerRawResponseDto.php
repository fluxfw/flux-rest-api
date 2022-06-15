<?php

namespace FluxRestApi\Adapter\Server;

use FluxRestApi\Adapter\Cookie\CookieDto;
use FluxRestApi\Adapter\Header\HeaderKey;
use FluxRestApi\Adapter\Status\LegacyDefaultStatus;
use FluxRestApi\Adapter\Status\Status;

class ServerRawResponseDto
{

    public ?string $body;
    /**
     * @var CookieDto[]
     */
    public array $cookies;
    /**
     * @var string[]
     */
    public array $headers;
    public ?string $sendfile;
    public Status $status;


    /**
     * @param string[]    $headers
     * @param CookieDto[] $cookies
     */
    private function __construct(
        /*public readonly*/ ?string $body,
        /*public readonly*/ Status $status,
        /*public readonly*/ array $headers,
        /*public readonly*/ array $cookies,
        /*public readonly*/ ?string $sendfile
    ) {
        $this->body = $body;
        $this->status = $status;
        $this->headers = $headers;
        $this->cookies = $cookies;
        $this->sendfile = $sendfile;
    }


    /**
     * @param string[]|null    $headers
     * @param CookieDto[]|null $cookies
     */
    public static function new(
        ?string $body = null,
        ?Status $status = null,
        ?array $headers = null,
        ?array $cookies = null,
        ?string $sendfile = null
    ) : static {
        $headers ??= [];

        return new static(
            $body,
            $status ?? LegacyDefaultStatus::_200(),
            array_combine(array_map("strtolower", array_keys($headers)), $headers),
            $cookies ?? [],
            $sendfile
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
