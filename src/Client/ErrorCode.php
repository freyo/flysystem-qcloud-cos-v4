<?php

namespace Freyo\Flysystem\QcloudCOSv4\Client;

class ErrorCode
{
    const COSAPI_SUCCESS         = 0;
    const COSAPI_PARAMS_ERROR    = -1;
    const COSAPI_NETWORK_ERROR   = -2;
    const COSAPI_INTEGRITY_ERROR = -3;
}
