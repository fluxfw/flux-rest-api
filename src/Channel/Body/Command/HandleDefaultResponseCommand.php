<?php

namespace FluxRestApi\Channel\Body\Command;

use FluxRestApi\Adapter\Header\LegacyDefaultHeader;
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

        http_response_code($response->getStatus()->value);

        $headers = $response->getHeaders();

        if ($response->getSendfile() !== null) {
            if ($server_type->value === LegacyDefaultServerType::NGINX()->value) {
                $headers[LegacyDefaultHeader::X_ACCEL_REDIRECT()->value] = $response->getSendfile();
            } else {
                $headers[LegacyDefaultHeader::X_SENDFILE()->value] = $response->getSendfile();
            }
            $headers[LegacyDefaultHeader::CONTENT_TYPE()->value] = "";
        }

        foreach ($headers as $key => $value) {
            header($key . ":" . $value);
        }

        foreach ($response->getCookies() as $cookie) {
            if ($cookie->getValue() !== null) {
                setcookie(
                    $cookie->getName(),
                    $cookie->getValue(),
                    $cookie->getExpiresIn() !== null ? (time() + $cookie->getExpiresIn()) : 0,
                    $cookie->getPath(),
                    $cookie->getDomain(),
                    $cookie->isSecure(),
                    $cookie->isHttpOnly()
                );
            } else {
                setcookie(
                    $cookie->getName(),
                    "",
                    0,
                    $cookie->getPath(),
                    $cookie->getDomain()
                );
            }
        }

        if ($response->getBody() !== null) {
            echo $response->getBody();
        }
    }
}
