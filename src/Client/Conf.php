<?php

namespace Freyo\Flysystem\QcloudCOSv4\Client;

class Conf
{
    // Cos php sdk version number.
    const VERSION = 'v4.2.3';
    const API_COSAPI_END_POINT = 'http://region.file.myqcloud.com/files/v2/';

    // Please refer to http://console.qcloud.com/cos to fetch your app_id, secret_id and secret_key.
    private static $APPID;
    private static $SECRET_ID;
    private static $SECRET_KEY;

    public static function setAppId($appId)
    {
        self::$APPID = $appId;
    }

    public static function setSecretId($secretId)
    {
        self::$SECRET_ID = $secretId;
    }

    public static function setSecretKey($secretKey)
    {
        self::$SECRET_KEY = $secretKey;
    }

    public static function getAppId()
    {
        return self::$APPID;
    }

    public static function getSecretId()
    {
        return self::$SECRET_ID;
    }

    public static function getSecretKey()
    {
        return self::$SECRET_KEY;
    }

    /**
     * Get the User-Agent string to send to COS server.
     */
    public static function getUserAgent()
    {
        return 'cos-php-sdk-'.self::VERSION;
    }
}
