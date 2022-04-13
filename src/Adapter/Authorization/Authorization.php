<?php

namespace FluxRestApi\Adapter\Authorization;

use FluxRestApi\Adapter\Server\ServerRawRequestDto;
use FluxRestApi\Adapter\Server\ServerResponseDto;

interface Authorization
{

    public function authorize(ServerRawRequestDto $request) : ?ServerResponseDto;
}
