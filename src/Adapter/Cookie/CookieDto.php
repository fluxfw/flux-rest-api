<?php

namespace FluxRestApi\Adapter\Cookie;

use FluxRestApi\Adapter\Cookie\Priority\CookiePriority;
use FluxRestApi\Adapter\Cookie\SameSite\CookieSameSite;

class CookieDto
{

    public string $domain;
    public ?int $expires_in;
    public bool $http_only;
    public string $name;
    public string $path;
    public ?CookiePriority $priority;
    public ?CookieSameSite $same_site;
    public bool $secure;
    public ?string $value;


    private function __construct(
        /*public readonly*/ string $name,
        /*public readonly*/ ?string $value,
        /*public readonly*/ ?int $expires_in,
        /*public readonly*/ string $path,
        /*public readonly*/ string $domain,
        /*public readonly*/ bool $secure,
        /*public readonly*/ bool $http_only,
        /*public readonly*/ ?CookieSameSite $same_site,
        /*public readonly*/ ?CookiePriority $priority
    ) {
        $this->name = $name;
        $this->value = $value;
        $this->expires_in = $expires_in;
        $this->path = $path;
        $this->domain = $domain;
        $this->secure = $secure;
        $this->http_only = $http_only;
        $this->same_site = $same_site;
        $this->priority = $priority;
    }


    public static function new(
        string $name,
        ?string $value = null,
        ?int $expires_in = null,
        ?string $path = null,
        ?string $domain = null,
        ?bool $secure = null,
        ?bool $http_only = null,
        ?CookieSameSite $same_site = null,
        ?CookiePriority $priority = null
    ) : static {
        return new static(
            $name,
            $value,
            $expires_in,
            $path ?? "/",
            $domain ?? "",
            $secure ?? true,
            $http_only ?? true,
            $same_site,
            $priority
        );
    }
}
