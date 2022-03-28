<?php

namespace FluxRestApi\Adapter\Server;

class SwooleRestApiServerConfigDto
{

    private ?string $https_cert;
    private ?string $https_key;
    private string $listen;
    private ?int $max_upload_size;
    private int $port;


    private function __construct(
        /*public readonly*/ ?string $https_cert,
        /*public readonly*/ ?string $https_key,
        /*public readonly*/ string $listen,
        /*public readonly*/ int $port,
        /*public readonly*/ ?int $max_upload_size
    ) {
        $this->https_cert = $https_cert;
        $this->https_key = $https_key;
        $this->listen = $listen;
        $this->port = $port;
        $this->max_upload_size = $max_upload_size;
    }


    public static function new(
        ?string $https_cert = null,
        ?string $https_key = null,
        ?string $listen = null,
        ?int $port = null,
        ?int $max_upload_size = null
    ) : /*static*/ self
    {
        return new static(
            $https_cert,
            $https_key,
            $listen ?? "0.0.0.0",
            $port ?? 9501,
            $max_upload_size
        );
    }


    public function getHttpsCert() : ?string
    {
        return $this->https_cert;
    }


    public function getHttpsKey() : ?string
    {
        return $this->https_key;
    }


    public function getListen() : string
    {
        return $this->listen;
    }


    public function getMaxUploadSize() : ?int
    {
        return $this->max_upload_size;
    }


    public function getPort() : int
    {
        return $this->port;
    }
}
