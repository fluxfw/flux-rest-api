<?php

namespace FluxRestApi;

require_once __DIR__ . "/../libs/flux-rest-base-api/autoload.php";

use FluxAutoloadApi\Adapter\Autoload\Psr4Autoload;

Psr4Autoload::new(
    [
        __NAMESPACE__ => __DIR__
    ]
)
    ->autoload();
