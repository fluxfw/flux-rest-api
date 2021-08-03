<?php

namespace Fluxlabs\FluxRestApi\Authorization;

use Fluxlabs\FluxRestApi\Request\RawRequestDto;

interface Authorization
{

    public function authorize(RawRequestDto $request) : void;


    public function get401Headers() : array;
}
