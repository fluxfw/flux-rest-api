<?php

namespace FluxRestApi\Channel\Body\Port;

use FluxRestApi\Adapter\Body\BodyDto;
use FluxRestApi\Adapter\Body\RawBodyDto;
use FluxRestApi\Adapter\Server\ServerRawRequestDto;
use FluxRestApi\Adapter\Server\ServerRawResponseDto;
use FluxRestApi\Adapter\ServerType\ServerType;
use FluxRestApi\Channel\Body\Command\GetDefaultRequestCommand;
use FluxRestApi\Channel\Body\Command\HandleDefaultResponseCommand;
use FluxRestApi\Channel\Body\Command\ParseBodyCommand;
use FluxRestApi\Channel\Body\Command\ToRawBodyCommand;

class BodyService
{

    private function __construct()
    {

    }


    public static function new() : /*static*/ self
    {
        return new static();
    }


    public function getDefaultRequest(?bool $rest_api_server = null) : ServerRawRequestDto
    {
        return GetDefaultRequestCommand::new()
            ->getDefaultRequest(
                $rest_api_server
            );
    }


    public function handleDefaultResponse(ServerRawResponseDto $response, ServerType $server_type) : void
    {
        HandleDefaultResponseCommand::new()
            ->handleDefaultResponse(
                $response,
                $server_type
            );
    }


    public function parseBody(RawBodyDto $body, ?array $post = null, ?array $files = null) : ?BodyDto
    {
        return ParseBodyCommand::new()
            ->parseBody(
                $body,
                $post,
                $files
            );
    }


    public function toRawBody(BodyDto $body) : RawBodyDto
    {
        return ToRawBodyCommand::new()
            ->toRawBody(
                $body
            );
    }
}
