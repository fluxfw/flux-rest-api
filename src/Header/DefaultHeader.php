<?php

namespace FluxRestApi\Header;

enum DefaultHeader: string implements Header
{

    case ACCEPT = "accept";
    case AUTHORIZATION = "authorization";
    case CONTENT_TYPE = "content-type";
    case LOCATION = "location";
    case USER_AGENT = "user-agent";
    case WWW_AUTHENTICATE = "www-authenticate";
    case X_ACCEL_REDIRECT = "x-accel-redirect";
    case X_HTTP_METHOD_OVERRIDE = "x-http-method-override";
    case X_SENDFILE = "x-sendfile";
}
