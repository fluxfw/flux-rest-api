<?php

namespace FluxRestApi\Adapter\Server;

class SwooleServerConfigDto
{

    private function __construct(
        public readonly ?string $https_cert,
        public readonly ?string $https_key,
        public readonly string $listen,
        public readonly int $port,
        public readonly ?int $max_upload_size
    ) {

    }


    public static function new(
        ?string $https_cert = null,
        ?string $https_key = null,
        ?string $listen = null,
        ?int $port = null,
        ?int $max_upload_size = null
    ) : static {
        return new static(
            $https_cert,
            $https_key,
            $listen ?? "0.0.0.0",
            $port ?? 9501,
            $max_upload_size
        );
    }
}
