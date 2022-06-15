<?php

namespace FluxRestApi\Adapter\Client;

use FluxRestApi\Adapter\Header\HeaderKey;
use FluxRestApi\Adapter\Method\LegacyDefaultMethod;
use FluxRestApi\Adapter\Method\Method;

class ClientRequestDto
{

    public ?string $body;
    public bool $fail_on_status_400_or_higher;
    public bool $follow_redirect;
    /**
     * @var string[]
     */
    public array $headers;
    public Method $method;
    /**
     * @var string[]
     */
    public array $query_params;
    public bool $response;
    public bool $trust_self_signed_certificate;
    public string $url;


    /**
     * @param string[] $query_params
     * @param string[] $headers
     */
    private function __construct(
        /*public readonly*/ string $url,
        /*public readonly*/ Method $method,
        /*public readonly*/ array $query_params,
        /*public readonly*/ ?string $body,
        /*public readonly*/ array $headers,
        /*public readonly*/ bool $response,
        /*public readonly*/ bool $fail_on_status_400_or_higher,
        /*public readonly*/ bool $follow_redirect,
        /*public readonly*/ bool $trust_self_signed_certificate
    ) {
        $this->url = $url;
        $this->method = $method;
        $this->query_params = $query_params;
        $this->body = $body;
        $this->headers = $headers;
        $this->response = $response;
        $this->fail_on_status_400_or_higher = $fail_on_status_400_or_higher;
        $this->follow_redirect = $follow_redirect;
        $this->trust_self_signed_certificate = $trust_self_signed_certificate;
    }


    /**
     * @param string[]|null $query_params
     * @param string[]|null $headers
     */
    public static function new(
        string $url,
        ?Method $method = null,
        ?array $query_params = null,
        ?string $body = null,
        ?array $headers = null,
        ?bool $response = null,
        ?bool $fail_on_status_400_or_higher = null,
        ?bool $follow_redirect = null,
        ?bool $trust_self_signed_certificate = null
    ) : static {
        $headers ??= [];

        return new static(
            $url,
            $method ?? LegacyDefaultMethod::GET(),
            $query_params ?? [],
            $body,
            array_combine(array_map("strtolower", array_keys($headers)), $headers),
            $response ?? true,
            $fail_on_status_400_or_higher ?? true,
            $follow_redirect ?? true,
            $trust_self_signed_certificate ?? false
        );
    }


    public function getHeader(HeaderKey $key) : ?string
    {
        return $this->headers[strtolower($key->value)] ?? null;
    }


    public function getQueryParam(string $name) : ?string
    {
        return $this->query_params[$name] ?? null;
    }
}
