<?php

namespace FluxRestApi;

require_once __DIR__ . "/../libs/FluxAutoloadApi/autoload.php";

use FluxAutoloadApi\Adapter\Autoload\PhpExtChecker;
use FluxAutoloadApi\Adapter\Autoload\PhpVersionChecker;
use FluxAutoloadApi\Adapter\Autoload\Psr4Autoload;

PhpVersionChecker::new(
    ">=7.4",
    __NAMESPACE__
)
    ->check();
PhpExtChecker::new(
    [
        "curl",
        "json"
    ],
    __NAMESPACE__
)
    ->check();

Psr4Autoload::new(
    [
        __NAMESPACE__ => __DIR__
    ]
)
    ->autoload();
