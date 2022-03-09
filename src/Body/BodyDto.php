<?php

namespace FluxRestApi\Body;

use FluxRestApi\Libs\FluxRestBaseApi\Body\BodyType;

interface BodyDto
{

    public function getType() : BodyType;
}
