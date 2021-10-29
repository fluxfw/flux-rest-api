<?php

namespace FluxRestApi\Log;

use Throwable;

trait Log
{

    private function log(Throwable $ex) : void
    {
        file_put_contents("php://stdout", $ex);
    }
}
