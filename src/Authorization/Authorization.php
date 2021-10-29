<?php

namespace FluxRestApi\Authorization;

use FluxRestApi\Request\RawRequestDto;
use FluxRestApi\Response\ResponseDto;

interface Authorization
{

    public function authorize(RawRequestDto $request) : ?ResponseDto;
}
