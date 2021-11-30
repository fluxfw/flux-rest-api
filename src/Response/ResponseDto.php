<?php

namespace FluxRestApi\Response;

use FluxRestApi\Body\BodyDto;
use FluxRestBaseApi\Status\LegacyDefaultStatus;
use FluxRestBaseApi\Status\Status;

class ResponseDto
{

    private ?BodyDto $body;
    private array $cookies;
    private array $headers;
    private ?string $raw_body;
    private ?string $sendfile;
    private Status $status;


    public static function new(?BodyDto $body = null, ?Status $status = null, ?array $headers = null, ?array $cookies = null, ?string $sendfile = null, ?string $raw_body = null) : /*static*/ self
    {
        $dto = new static();

        $dto->body = $body;
        $dto->status = $status ?? LegacyDefaultStatus::_200();
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


    public function getStatus() : Status
    {
        return $this->status;
    }
}
