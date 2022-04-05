<?php

namespace FluxRestApi\Method;

enum DefaultMethod: string implements Method
{

    case DELETE = "DELETE";
    case GET = "GET";
    case PATCH = "PATCH";
    case POST = "POST";
    case PUT = "PUT";
}
