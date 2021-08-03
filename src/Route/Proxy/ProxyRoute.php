<?php

namespace Fluxlabs\FluxRestApi\Route\Proxy;

use Fluxlabs\FluxRestApi\Body\BodyHelper;
use Fluxlabs\FluxRestApi\Body\Raw\RawBodyDto;
use Fluxlabs\FluxRestApi\Request\RawRequestDto;
use Fluxlabs\FluxRestApi\Request\RequestDto;
use Fluxlabs\FluxRestApi\Response\ResponseDto;

trait ProxyRoute
{

    use BodyHelper;

    public final function handle(RequestDto $request) : ResponseDto
    {
        $curl = null;
        try {
            $url = $this->getProxyUrl();
            if (!empty($request->getQuery())) {
                $url .= (str_contains($url, "?") ? "&" : "?")
                    . implode("&", array_map(fn(string $key, string $value) : string => rawurlencode($key) . "=" . rawurlencode($value), array_keys($request->getQuery()), $request->getQuery()));
            }

            $curl = curl_init($url);

            $headers = $request->getHeaders();

            $body = $this->toRawBody(
                $request->getBody()
            );
            if ($body !== null) {
                $headers["Content-Type"] = $body->getType();
                curl_setopt($curl, CURLOPT_POSTFIELDS, $body->getBody());
            }

            curl_setopt($curl, CURLOPT_HTTPHEADER, array_map(fn(string $key, string $value) : string => $key . ": " . $value, array_keys($headers), $headers));

            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request->getMethod());

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $response_headers = [];
            curl_setopt($curl, CURLOPT_HEADERFUNCTION, function ($curl, $header) use (&$response_headers) : int {
                $len = strlen($header);

                $header = array_filter(array_map("trim", explode(":", $header, 2)));

                if (count($header) === 2) {
                    $response_headers[$header[0]] = $header[1];
                }

                return $len;
            });

            $response_request = RawRequestDto::new(
                "",
                "",
                null,
                $result = curl_exec($curl),
                $response_headers,
                null
            );

            return ResponseDto::new(
                RawBodyDto::new(
                    $response_request->getHeader("Content-Type") ?? "",
                    $response_request->getBody() ?? ""
                ),
                curl_getinfo($curl, CURLINFO_HTTP_CODE),
                $response_request->getHeaders()
            );
        } finally {
            if ($curl !== null) {
                curl_close($curl);
            }
        }
    }


    protected abstract function getProxyUrl() : string;
}
