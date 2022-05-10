<?php

namespace FluxRestApi\Adapter\Body\Type;

use FluxRestApi\Libs\FluxLegacyEnum\Adapter\Backed\LegacyStringBackedEnum;

/**
 * @method static static FORM_DATA() application/x-www-form-urlencoded
 * @method static static FORM_DATA_2() multipart/form-data
 * @method static static HTML() text/html
 * @method static static JSON() application/json
 * @method static static TEXT() text/plain
 */
class LegacyDefaultBodyType extends LegacyStringBackedEnum implements BodyType
{

}
