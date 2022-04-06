<?php

namespace FluxRestApi\Adapter\Cookie\SameSite;

enum DefaultCookieSameSite: string implements CookieSameSite
{

    case LAX = "Lax";
    case NONE = "None";
    case STRICT = "Strict";
}
