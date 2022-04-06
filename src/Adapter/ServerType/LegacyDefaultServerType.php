<?php

namespace FluxRestApi\Adapter\ServerType;

use FluxRestApi\Libs\FluxLegacyEnum\Adapter\Backed\LegacyStringBackedEnum;

/**
 * @method static static APACHE() apache
 * @method static static NGINX() nginx
 * @method static static SWOOLE() swoole
 */
class LegacyDefaultServerType extends LegacyStringBackedEnum implements ServerType
{

}
