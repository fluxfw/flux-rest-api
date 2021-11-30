<?php

namespace FluxRestApi\Cookie;

enum DefaultCookiePriority: string implements CookiePriority
{

    case HIGH = "High";
    case LOW = "Low";
    case MEDIUM = "Medium";
}
