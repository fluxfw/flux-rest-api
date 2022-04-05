<?php

namespace FluxRestApi\Status;

use FluxRestApi\Libs\FluxLegacyEnum\Adapter\Backed\LegacyIntBackedEnum;

/**
 * @method static static _200() 200
 * @method static static _201() 201
 * @method static static _302() 302
 * @method static static _400() 400
 * @method static static _401() 401
 * @method static static _403() 403
 * @method static static _404() 404
 * @method static static _405() 405
 * @method static static _500() 500
 */
class LegacyDefaultStatus extends LegacyIntBackedEnum implements Status
{

}
