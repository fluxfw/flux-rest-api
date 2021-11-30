<?php

namespace FluxRestApi\Cookie;

use FluxLegacyEnum\Adapter\Backed\LegacyStringBackedEnum;

/**
 * @method static static LAX() Lax
 * @method static static NONE() None
 * @method static static STRICT() Strict
 */
class LegacyDefaultCookieSameSite extends LegacyStringBackedEnum implements CookieSameSite
{

}
