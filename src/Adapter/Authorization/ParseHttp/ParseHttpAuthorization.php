<?php

namespace FluxRestApi\Adapter\Authorization\ParseHttp;

use FluxRestApi\Adapter\Authorization\Schema\CustomAuthorizationSchema;
use FluxRestApi\Adapter\Body\TextBodyDto;
use FluxRestApi\Adapter\Header\LegacyDefaultHeaderKey;
use FluxRestApi\Adapter\Server\ServerRawRequestDto;
use FluxRestApi\Adapter\Server\ServerResponseDto;
use FluxRestApi\Adapter\Status\LegacyDefaultStatus;

trait ParseHttpAuthorization
{

    /**
     * @return HttpAuthorizationDto|ServerResponseDto
     */
    private function parseHttpAuthorization(ServerRawRequestDto $request, HttpAuthorizationDto $www_authenticate_header)/* : HttpAuthorizationDto|ServerResponseDto*/
    {
        $authorization = $request->getHeader(
            LegacyDefaultHeaderKey::AUTHORIZATION()
        );

        if (empty($authorization)) {
            return ServerResponseDto::new(
                TextBodyDto::new(
                    "Authorization needed"
                ),
                LegacyDefaultStatus::_401(),
                [
                    LegacyDefaultHeaderKey::WWW_AUTHENTICATE()->value => $www_authenticate_header->getSchema()->value . (!empty($www_authenticate_header->getParameters())
                            ? ParseHttpAuthorization_::SPLIT_SCHEMA_PARAMETERS
                            . $www_authenticate_header->getParameters() : "")
                ]
            );
        }

        if (!str_contains($authorization, ParseHttpAuthorization_::SPLIT_SCHEMA_PARAMETERS)) {
            return ServerResponseDto::new(
                TextBodyDto::new(
                    "Missing authorization schema or parameters"
                ),
                LegacyDefaultStatus::_400()
            );
        }

        $parameters = explode(ParseHttpAuthorization_::SPLIT_SCHEMA_PARAMETERS, $authorization);
        $schema = array_shift($parameters);
        $parameters = implode(ParseHttpAuthorization_::SPLIT_SCHEMA_PARAMETERS, $parameters);

        if (empty($schema) || empty($parameters)) {
            return ServerResponseDto::new(
                TextBodyDto::new(
                    "Missing authorization schema or parameters"
                ),
                LegacyDefaultStatus::_400()
            );
        }

        return HttpAuthorizationDto::new(
            CustomAuthorizationSchema::factory(
                $schema
            ),
            $parameters
        );
    }
}
