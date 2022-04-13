<?php

namespace FluxRestApi\Adapter\Cookie\Priority;

enum DefaultCookiePriority: string implements CookiePriority
{

    case HIGH = "High";
    case LOW = "Low";
    case MEDIUM = "Medium";
}
