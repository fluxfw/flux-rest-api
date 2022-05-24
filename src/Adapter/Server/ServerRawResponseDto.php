<?php

namespace FluxRestApi\Adapter\Server;

use FluxRestApi\Adapter\Cookie\CookieDto;
use FluxRestApi\Adapter\Header\HeaderKey;
use FluxRestApi\Adapter\Status\LegacyDefaultStatus;
use FluxRestApi\Adapter\Status\Status;

class ServerRawResponseDto
{

    private ?string $body;
    /**
     * @var CookieDto[]
     */
    private array $cookies;
    /**
     * @var string[]
     */
    private array $headers;
    private ?string $sendfile;
    private Status $status;


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
    ) : /*static*/ self
    {
        $headers ??= [];

        return new static(
            $body,
            $status ?? LegacyDefaultStatus::_200(),
            array_combine(array_map("strtolower", array_keys($headers)), $headers),
            $cookies ?? [],
            $sendfile
        );
    }


    public function getBody() : ?string
    {
        return $this->body;
    }


    public function getCookie(string $name) : ?CookieDto
    {
        return $this->cookies[$name] ?? null;
    }


    /**
     * @return CookieDto[]
     */
    public function getCookies() : array
    {
        return $this->cookies;
    }


    public function getHeader(HeaderKey $key) : ?string
    {
        return $this->headers[strtolower($key->value)] ?? null;
    }


    /**
     * @return string[]
     */
    public function getHeaders() : array
    {
        return $this->headers;
    }


    public function getSendfile() : ?string
    {
        return $this->sendfile;
    }


    public function getStatus() : Status
    {
        return $this->status;
    }
}
