<?php

namespace FluxRestApi\Channel\Client\Port;

use FluxRestApi\Adapter\Client\ClientRequestDto;
use FluxRestApi\Adapter\Client\ClientResponseDto;
use FluxRestApi\Channel\Client\Command\MakeRequestCommand;

class ClientService
{

    private function __construct()
    {

    }


    public static function new() : /*static*/ self
    {
        return new static();
    }


    public function makeRequest(ClientRequestDto $request) : ?ClientResponseDto
    {
        return MakeRequestCommand::new()
            ->makeRequest(
                $request
            );
    }
}
