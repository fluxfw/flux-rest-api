<?php

namespace FluxRestApi\Server;

enum DefaultServer: string implements Server
{

    case APACHE = "apache";
    case NGINX = "nginx";
    case SWOOLE = " swoole";
}
