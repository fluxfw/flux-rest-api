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
        $dto = new static();

        $dto->name = $name;
        $dto->value = $value;
        $dto->expires_in = $expires_in;
        $dto->path = $path ?? "/";
        $dto->domain = $domain ?? "";
        $dto->secure = $secure ?? true;
        $dto->http_only = $http_only ?? true;
        $dto->same_site = $same_site;
        $dto->priority = $priority;

        return $dto;
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
