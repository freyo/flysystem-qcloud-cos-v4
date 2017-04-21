<?php

namespace Freyo\Flysystem\QcloudCOSv4\Client;

class HttpResponse
{
    public $curlErrorCode;    // int: curl last error code.
    public $curlErrorMessage; // string: curl last error message.
    public $statusCode;       // int: http status code.
    public $headers;          // array: response headers.
    public $body;             // string: response body.
}
