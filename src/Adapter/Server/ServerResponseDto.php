<?php

namespace FluxRestApi\Adapter\Server;

use FluxRestApi\Adapter\Body\BodyDto;
use FluxRestApi\Adapter\Cookie\CookieDto;
use FluxRestApi\Adapter\Status\LegacyDefaultStatus;
use FluxRestApi\Adapter\Status\Status;

class ServerResponseDto
{

    private ?BodyDto $body;
    /**
     * @var CookieDto[]
     */
    private array $cookies;
    /**
     * @var string[]
     */
    private array $headers;
    private ?string $raw_body;
    private ?string $sendfile;
    private Status $status;


    /**
     * @param string[]    $headers
     * @param CookieDto[] $cookies
     */
    private function __construct(
        /*public readonly*/ ?BodyDto $body,
        /*public readonly*/ Status $status,
        /*public readonly*/ array $headers,
        /*public readonly*/ array $cookies,
        /*public readonly*/ ?string $sendfile,
        /*public readonly*/ ?string $raw_body
    ) {
        $this->body = $body;
        $this->status = $status;
        $this->headers = $headers;
        $this->cookies = $cookies;
        $this->sendfile = $sendfile;
        $this->raw_body = $raw_body;
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
    ) : /*static*/ self
    {
        $headers ??= [];

        return new static(
            $body,
            $status ?? LegacyDefaultStatus::_200(),
            array_combine(array_map("strtolower", array_keys($headers)), $headers),
            $cookies ?? [],
            $sendfile,
            $raw_body
        );
    }


    public function getBody() : ?BodyDto
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


    public function getRawBody() : ?string
    {
        return $this->raw_body;
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
