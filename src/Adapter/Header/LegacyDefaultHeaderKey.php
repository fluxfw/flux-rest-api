<?php

namespace FluxRestApi\Adapter\Header;

use FluxRestApi\Libs\FluxLegacyEnum\Adapter\Backed\LegacyStringBackedEnum;

/**
 * @method static static ACCEPT() accept
 * @method static static AUTHORIZATION() authorization
 * @method static static CONTENT_DISPOSITION() content-disposition
 * @method static static CONTENT_TYPE() content-type
 * @method static static LOCATION() location
 * @method static static USER_AGENT() user-agent
 * @method static static WWW_AUTHENTICATE() www-authenticate
 * @method static static X_ACCEL_REDIRECT() x-accel-redirect
 * @method static static X_HTTP_METHOD_OVERRIDE() x-http-method-override
 * @method static static X_SENDFILE() x-sendfile
 */
class LegacyDefaultHeaderKey extends LegacyStringBackedEnum implements HeaderKey
{

}
