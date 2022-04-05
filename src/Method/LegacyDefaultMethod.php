<?php

namespace FluxRestApi\Method;

use FluxRestApi\Libs\FluxLegacyEnum\Adapter\Backed\LegacyStringBackedEnum;

/**
 * @method static static DELETE() DELETE
 * @method static static GET() GET
 * @method static static PATCH() PATCH
 * @method static static POST() POST
 * @method static static PUT() PUT
 */
class LegacyDefaultMethod extends LegacyStringBackedEnum implements Method
{

}
