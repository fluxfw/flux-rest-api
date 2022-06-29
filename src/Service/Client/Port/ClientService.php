<?php

namespace FluxRestApi\Service\Client\Port;

use FluxRestApi\Adapter\Client\ClientRequestDto;
use FluxRestApi\Adapter\Client\ClientResponseDto;
use FluxRestApi\Service\Body\Port\BodyService;
use FluxRestApi\Service\Client\Command\MakeRequestCommand;

class ClientService
{

    private function __construct(
        private readonly BodyService $body_service
    ) {

    }


    public static function new(
        BodyService $body_service
    ) : static {
        return new static(
            $body_service
        );
    }


    public function makeRequest(ClientRequestDto $request) : ?ClientResponseDto
    {
        return MakeRequestCommand::new(
            $this->body_service
        )
            ->makeRequest(
                $request
            );
    }
}
