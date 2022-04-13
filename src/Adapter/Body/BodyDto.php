<?php

namespace FluxRestApi\Adapter\Body;

use FluxRestApi\Adapter\Body\Type\BodyType;

interface BodyDto
{

    public function getType() : BodyType;
}
