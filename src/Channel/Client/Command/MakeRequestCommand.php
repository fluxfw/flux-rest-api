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
            $url = $request->getUrl();

            if (!empty($request->getQueryParams())) {
                $url .= (str_contains($url, "?") ? "&" : "?") . implode("&",
                        array_map(fn(string $key, string $value) : string => rawurlencode($key) . "=" . rawurlencode($value), array_keys($request->getQueryParams()), $request->getQueryParams()));
            }

            $curl = curl_init($url);

            if ($request->isFailOnStatus400OrHigher()) {
                curl_setopt($curl, CURLOPT_FAILONERROR, true);
            }

            if ($request->isFollowRedirect()) {
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            }

            if ($request->isTrustSelfSignedCertificate()) {
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_PROXY_SSL_VERIFYHOST, false);
            }

            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request->getMethod()->value);

            if ($request->getBody() !== null) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $request->getBody());
            }

            if (!empty($request->getHeaders())) {
                curl_setopt($curl, CURLOPT_HTTPHEADER, array_map(fn(string $key, string $value) : string => $key . ":" . $value, array_keys($request->getHeaders()), $request->getHeaders()));
            }

            $headers = [];
            if ($request->isResponse()) {
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

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

            if ($request->isResponse()) {
                return ClientResponseDto::new(
                    CustomStatus::factory(curl_getinfo($curl, CURLINFO_HTTP_CODE)),
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
