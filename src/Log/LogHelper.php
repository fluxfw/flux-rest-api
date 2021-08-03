<?php

namespace Fluxlabs\FluxRestApi\Log;

use Throwable;

trait LogHelper
{

    private function log(Throwable $ex) : void
    {
        file_put_contents("php://stdout", $ex);
    }
}
