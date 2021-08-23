<?php

namespace Fluxlabs\FluxRestApi\Response;

use Fluxlabs\FluxRestApi\Body\BodyDto;

class ResponseDto
{

    private ?BodyDto $body;
    private array $cookies;
    private array $headers;
    private ?string $raw_body;
    private ?string $sendfile;
    private int $status;


    public static function new(?BodyDto $body = null, ?int $status = null, ?array $headers = null, ?array $cookies = null, ?string $sendfile = null, ?string $raw_body = null) : /*static*/ self
    {
        $dto = new static();

        $dto->body = $body;
        $dto->status = $status ?? 200;
        $dto->headers = $headers ?? [];
        $dto->cookies = $cookies ?? [];
        $dto->sendfile = $sendfile;
        $dto->raw_body = $raw_body;

        return $dto;
    }


    public function getBody() : ?BodyDto
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


    public function getRawBody() : ?string
    {
        return $this->raw_body;
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
