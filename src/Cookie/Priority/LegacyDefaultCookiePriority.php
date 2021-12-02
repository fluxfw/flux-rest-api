<?php

namespace FluxRestApi\Cookie\Priority;

use FluxLegacyEnum\Adapter\Backed\LegacyStringBackedEnum;

/**
 * @method static static HIGH() High
 * @method static static LOW() Low
 * @method static static MEDIUM() Medium
 */
class LegacyDefaultCookiePriority extends LegacyStringBackedEnum implements CookiePriority
{

}
