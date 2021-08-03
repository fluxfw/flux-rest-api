<?php

namespace Fluxlabs\FluxRestApi\Response;

use Fluxlabs\FluxRestApi\Body\BodyDto;

class ResponseDto
{

    protected ?string $sendfile;
    private ?BodyDto $body;
    private array $cookies;
    private array $headers;
    private int $status;


    public static function new(?BodyDto $body = null, ?int $status = null, ?array $headers = null, ?array $cookies = null, ?string $sendfile = null) : /*static*/ self
    {
        $dto = new static();

        $dto->body = $body;
        $dto->status = $status ?? 200;
        $dto->headers = $headers ?? [];
        $dto->cookies = $cookies ?? [];
        $dto->sendfile = $sendfile;

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


    public function getSendfile() : ?string
    {
        return $this->sendfile;
    }


    public function getStatus() : int
    {
        return $this->status;
    }
}
