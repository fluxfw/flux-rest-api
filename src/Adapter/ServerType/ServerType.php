<?php

namespace FluxRestApi\Adapter\ServerType;

enum ServerType: string
{

    case APACHE = "apache";
    case NGINX = "nginx";
    case SWOOLE = "swoole";
}
