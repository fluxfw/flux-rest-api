<?php

namespace FluxRestApi\Response;

use FluxRestApi\Body\BodyDto;
use FluxRestApi\Libs\FluxRestBaseApi\Status\LegacyDefaultStatus;
use FluxRestApi\Libs\FluxRestBaseApi\Status\Status;

class ResponseDto
{

    private ?BodyDto $body;
    private array $cookies;
    private array $headers;
    private ?string $raw_body;
    private ?string $sendfile;
    private Status $status;


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


    public static function new(
        ?BodyDto $body = null,
        ?Status $status = null,
        ?array $headers = null,
        ?array $cookies = null,
        ?string $sendfile = null,
        ?string $raw_body = null
    ) : /*static*/ self
    {
        return new static(
            $body,
            $status ?? LegacyDefaultStatus::_200(),
            $headers ?? [],
            $cookies ?? [],
            $sendfile,
            $raw_body
        );
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
