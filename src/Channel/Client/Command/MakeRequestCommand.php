<?php

namespace FluxRestApi\Channel\Client\Command;

use Exception;
use FluxRestApi\Adapter\Client\ClientRequestDto;
use FluxRestApi\Adapter\Client\ClientResponseDto;
use FluxRestApi\Adapter\Status\CustomStatus;

class MakeRequestCommand
{

    private function __construct()
    {

    }


    public static function new() : /*static*/ self
    {
        return new static();
    }


    public function makeRequest(ClientRequestDto $request) : ?ClientResponseDto
    {
        $curl = null;
        try {
            $url = $request->url;

            if (!empty($request->query_params)) {
                $url .= (str_contains($url, "?") ? "&" : "?") . implode("&",
                        array_map(fn(string $key, string $value) : string => rawurlencode($key) . "=" . rawurlencode($value), array_keys($request->query_params), $request->query_params));
            }

            $curl = curl_init($url);

            if ($request->fail_on_status_400_or_higher) {
                curl_setopt($curl, CURLOPT_FAILONERROR, true);
            }

            if ($request->follow_redirect) {
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            }

            if ($request->trust_self_signed_certificate) {
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_PROXY_SSL_VERIFYHOST, false);
            }

            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request->method->value);

            if ($request->body !== null) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $request->body);
            }

            if (!empty($request->headers)) {
                curl_setopt($curl, CURLOPT_HTTPHEADER, array_map(fn(string $key, string $value) : string => $key . ":" . $value, array_keys($request->headers), $request->headers));
            }

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $headers = [];
            if ($request->response) {
                curl_setopt($curl, CURLOPT_HEADERFUNCTION, function (/*CurlHandle*/ $curl, string $header) use (&$headers) : int {
                    $len = strlen($header);

                    $header = array_filter(array_map("trim", explode(":", $header, 2)));

                    if (count($header) === 2) {
                        $headers[$header[0]] = $header[1];
                    }

                    return $len;
                });
            }

            $body = curl_exec($curl);

            $error_code = curl_errno($curl);
            if ($error_code !== 0) {
                throw new Exception(curl_error($curl), $error_code);
            }

            if ($request->response) {
                return ClientResponseDto::new(
                    CustomStatus::factory(
                        curl_getinfo($curl, CURLINFO_HTTP_CODE)
                    ),
                    $headers,
                    $body
                );
            } else {
                return null;
            }
        } finally {
            if ($curl !== null) {
                curl_close($curl);
            }
        }
    }
}
