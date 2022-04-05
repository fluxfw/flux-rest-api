<?php

namespace FluxRestApi\Adapter\Route;

use Exception;
use FluxRestApi\Body\FormDataBodyDto;
use FluxRestApi\Request\RequestDto;
use FluxRestApi\Response\ResponseDto;
use FluxRestApi\Status\CustomStatus;

trait ProxyRoute
{

    public final function handle(RequestDto $request) : ?ResponseDto
    {
        $curl = null;
        try {
            $url = preg_replace_callback("/{([A-Za-z0-9-_]+)}/", fn(array $matches) : string => $request->getParams()[$matches[1]] ?? $matches[0], $this->getProxyUrl());

            if (!empty($request->getQueryParams())) {
                $url .= (str_contains($url, "?") ? "&" : "?")
                    . implode("&",
                        array_map(fn(string $key, string $value) : string => rawurlencode($key) . "=" . rawurlencode($value), array_keys($request->getQueryParams()), $request->getQueryParams()));
            }

            $curl = curl_init($url);

            if ($request->getParsedBody() instanceof FormDataBodyDto) {
                throw new Exception("Proxy form data body is not supported");
            }

            curl_setopt($curl, CURLOPT_POSTFIELDS, $request->getRawBody());

            curl_setopt($curl, CURLOPT_HTTPHEADER, array_map(fn(string $key, string $value) : string => $key . ":" . $value, array_keys($request->getHeaders()), $request->getHeaders()));

            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request->getMethod()->value);

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $headers = [];
            curl_setopt($curl, CURLOPT_HEADERFUNCTION, function (/*CurlHandle*/ $curl, string $header) use (&$headers) : int {
                $len = strlen($header);

                $header = array_filter(array_map("trim", explode(":", $header, 2)));

                if (count($header) === 2) {
                    $headers[$header[0]] = $header[1];
                }

                return $len;
            });

            $raw_body = curl_exec($curl);

            if (curl_errno($curl) !== 0) {
                throw new Exception(curl_error($curl));
            }

            return ResponseDto::new(
                null,
                CustomStatus::factory(curl_getinfo($curl, CURLINFO_HTTP_CODE)),
                $headers,
                null,
                null,
                $raw_body
            );
        } finally {
            if ($curl !== null) {
                curl_close($curl);
            }
        }
    }


    protected abstract function getProxyUrl() : string;
}
