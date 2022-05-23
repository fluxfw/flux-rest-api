<?php

namespace FluxRestApi\Adapter\Authorization\Schema;

enum DefaultAuthorizationSchema: string implements AuthorizationSchema
{

    case BASIC = "Basic";
}
