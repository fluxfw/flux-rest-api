<?php

namespace FluxRestApi;

require_once __DIR__ . "/../libs/flux-autoload-api/autoload.php";
require_once __DIR__ . "/../libs/flux-legacy-enum/autoload.php";
require_once __DIR__ . "/../libs/flux-rest-base-api/autoload.php";

use FluxRestApi\Libs\FluxAutoloadApi\Adapter\Autoload\Psr4Autoload;
use FluxRestApi\Libs\FluxAutoloadApi\Adapter\Checker\PhpExtChecker;
use FluxRestApi\Libs\FluxAutoloadApi\Adapter\Checker\PhpVersionChecker;

PhpVersionChecker::new(
    ">=7.4"
)
    ->checkAndDie(
        __NAMESPACE__
    );
PhpExtChecker::new(
    [
        "curl",
        "json"
        //"swoole"
    ]
)
    ->checkAndDie(
        __NAMESPACE__
    );

Psr4Autoload::new(
    [
        __NAMESPACE__ => __DIR__
    ]
)
    ->autoload();
