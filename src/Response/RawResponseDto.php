<?php

namespace Fluxlabs\FluxRestApi\Response;

use Fluxlabs\FluxRestApi\Body\Raw\RawBodyDto;

class RawResponseDto
{

    protected ?string $sendfile;
    private ?RawBodyDto $body;
    private array $cookies;
    private array $headers;
    private int $status;


    public static function new(int $status, ?RawBodyDto $body = null, ?array $headers = null, ?array $cookies = null, ?string $sendfile = null) : /*static*/ self
    {
        $dto = new static();

        $dto->status = $status;
        $dto->body = $body;
        $dto->headers = $headers ?? [];
        $dto->cookies = $cookies ?? [];
        $dto->sendfile = $sendfile;

        return $dto;
    }


    public function getBody() : ?RawBodyDto
    {
        return $this->body;
    }


    public function getCookies() : array
    {
        return $this->cookies;
    }


    public function getHeaders() : array
    {
        return $this->headers;
    }


    public function getSendfile() : ?string
    {
        return $this->sendfile;
    }


    public function getStatus() : int
    {
        return $this->status;
    }
}
