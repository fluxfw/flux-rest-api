<?php

namespace FluxRestApi\Channel\Body\Command;

use FluxRestApi\Adapter\Header\LegacyDefaultHeaderKey;
use FluxRestApi\Adapter\Server\ServerRawResponseDto;
use FluxRestApi\Adapter\ServerType\LegacyDefaultServerType;
use FluxRestApi\Adapter\ServerType\ServerType;
use LogicException;

class HandleDefaultResponseCommand
{

    private function __construct()
    {

    }


    public static function new() : /*static*/ self
    {
        return new static();
    }


    public function handleDefaultResponse(ServerRawResponseDto $response, ServerType $server_type) : void
    {
        if (headers_sent($filename, $line)) {
            throw new LogicException("Do not manually output headers or body in " . $filename . ":" . $line);
        }

        http_response_code($response->status->value);

        $headers = $response->headers;

        if ($response->sendfile !== null) {
            if ($server_type->value === LegacyDefaultServerType::NGINX()->value) {
                $headers[LegacyDefaultHeaderKey::X_ACCEL_REDIRECT()->value] = $response->sendfile;
            } else {
                $headers[LegacyDefaultHeaderKey::X_SENDFILE()->value] = $response->sendfile;
            }
            $headers[LegacyDefaultHeaderKey::CONTENT_TYPE()->value] = "";
        }

        foreach ($headers as $key => $value) {
            header($key . ":" . $value);
        }

        foreach ($response->cookies as $cookie) {
            if ($cookie->value !== null) {
                setcookie(
                    $cookie->name,
                    $cookie->value,
                    $cookie->expires_in !== null ? (time() + $cookie->expires_in) : 0,
                    $cookie->path,
                    $cookie->domain,
                    $cookie->secure,
                    $cookie->http_only
                );
            } else {
                setcookie(
                    $cookie->name,
                    "",
                    0,
                    $cookie->path,
                    $cookie->domain
                );
            }
        }

        if ($response->body !== null) {
            echo $response->body;
        }
    }
}
