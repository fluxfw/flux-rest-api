<?php

namespace FluxRestApi;

spl_autoload_register(function (string $class) : void {
    if (str_starts_with($class, __NAMESPACE__ . "\\")) {
        require_once __DIR__ . str_replace("\\", "/", substr($class, strlen(__NAMESPACE__))) . ".php";
    }
});
