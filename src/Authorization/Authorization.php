<?php

namespace Fluxlabs\FluxRestApi\Authorization;

use Fluxlabs\FluxRestApi\Request\RawRequestDto;
use Fluxlabs\FluxRestApi\Response\ResponseDto;

interface Authorization
{

    public function authorize(RawRequestDto $request) : ?ResponseDto;
}
