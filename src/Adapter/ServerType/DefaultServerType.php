<?php

namespace FluxRestApi\Adapter\ServerType;

enum DefaultServerType: string implements ServerType
{

    case APACHE = "apache";
    case NGINX = "nginx";
    case SWOOLE = " swoole";
}
