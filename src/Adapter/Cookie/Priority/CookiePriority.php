<?php

namespace FluxRestApi\Adapter\Cookie\Priority;

enum CookiePriority: string
{

    case HIGH = "High";
    case LOW = "Low";
    case MEDIUM = "Medium";
}
