<?php

namespace FluxRestApi\Cookie;

use FluxRestApi\Cookie\Priority\CookiePriority;
use FluxRestApi\Cookie\SameSite\CookieSameSite;

class CookieDto
{

    private string $domain;
    private ?int $expires_in;
    private bool $http_only;
    private string $name;
    private string $path;
    private ?CookiePriority $priority;
    private ?CookieSameSite $same_site;
    private bool $secure;
    private ?string $value;


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
    ) : /*static*/ self
    {
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


    public function getDomain() : string
    {
        return $this->domain;
    }


    public function getExpiresIn() : ?int
    {
        return $this->expires_in;
    }


    public function getName() : string
    {
        return $this->name;
    }


    public function getPath() : string
    {
        return $this->path;
    }


    public function getPriority() : ?CookiePriority
    {
        return $this->priority;
    }


    public function getSameSite() : ?CookieSameSite
    {
        return $this->same_site;
    }


    public function getValue() : ?string
    {
        return $this->value;
    }


    public function isHttpOnly() : bool
    {
        return $this->http_only;
    }


    public function isSecure() : bool
    {
        return $this->secure;
    }
}
