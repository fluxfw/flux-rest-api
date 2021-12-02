<?php

namespace FluxRestApi\Cookie\Priority;

enum DefaultCookiePriority: string implements CookiePriority
{

    case HIGH = "High";
    case LOW = "Low";
    case MEDIUM = "Medium";
}
