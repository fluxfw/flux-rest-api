<?php

namespace FluxRestApi\Body;

use FluxRestBaseApi\Body\BodyType;

interface BodyDto
{

    public function getType() : BodyType;
}
