<?php

namespace Freyo\Flysystem\QcloudCOSv4\Client;

class HttpClient
{
    private static $httpInfo = '';
    private static $curlHandler;

    /**
     * send http request.
     *
     * @param array $request http请求信息
     *                       url        : 请求的url地址
     *                       method     : 请求方法，'get', 'post', 'put', 'delete', 'head'
     *                       data       : 请求数据，如有设置，则method为post
     *                       header     : 需要设置的http头部
     *                       host       : 请求头部host
     *                       timeout    : 请求超时时间
     *                       cert       : ca文件路径
     *                       ssl_version: SSL版本号
     *
     * @return string http请求响应
     */
    public static function sendRequest($request)
    {
        if (self::$curlHandler) {
            if (function_exists('curl_reset')) {
                curl_reset(self::$curlHandler);
            } else {
                LibcurlHelper::my_curl_reset(self::$curlHandler);
            }
        } else {
            self::$curlHandler = curl_init();
        }

        curl_setopt(self::$curlHandler, CURLOPT_URL, $request['url']);

        $method = 'GET';
        if (isset($request['method']) &&
            in_array(strtolower($request['method']), ['get', 'post', 'put', 'delete', 'head'])
        ) {
            $method = strtoupper($request['method']);
        } elseif (isset($request['data'])) {
            $method = 'POST';
        }

        $header = isset($request['header']) ? $request['header'] : [];
        $header[] = 'Method:'.$method;
        $header[] = 'User-Agent:'.Conf::getUserAgent();
        $header[] = 'Connection: keep-alive';

        isset($request['host']) && $header[] = 'Host:'.$request['host'];
        curl_setopt(self::$curlHandler, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(self::$curlHandler, CURLOPT_CUSTOMREQUEST, $method);
        isset($request['timeout']) && curl_setopt(self::$curlHandler, CURLOPT_TIMEOUT, $request['timeout']);

        if (isset($request['data']) && in_array($method, ['POST', 'PUT'])) {
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt(self::$curlHandler, CURLOPT_SAFE_UPLOAD, true);
            }

            curl_setopt(self::$curlHandler, CURLOPT_POST, true);
            array_push($header, 'Expect: 100-continue');

            if (is_array($request['data'])) {
                $arr = LibcurlHelper::buildCustomPostFields($request['data']);
                array_push($header, 'Content-Type: multipart/form-data; boundary='.$arr[0]);
                curl_setopt(self::$curlHandler, CURLOPT_POSTFIELDS, $arr[1]);
            } else {
                curl_setopt(self::$curlHandler, CURLOPT_POSTFIELDS, $request['data']);
            }
        }
        curl_setopt(self::$curlHandler, CURLOPT_HTTPHEADER, $header);

        $ssl = substr($request['url'], 0, 8) == 'https://' ? true : false;
        if (isset($request['cert'])) {
            curl_setopt(self::$curlHandler, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt(self::$curlHandler, CURLOPT_CAINFO, $request['cert']);
            curl_setopt(self::$curlHandler, CURLOPT_SSL_VERIFYHOST, 2);
            self::setCurlSSLVersion($request);
        } elseif ($ssl) {
            curl_setopt(self::$curlHandler, CURLOPT_SSL_VERIFYPEER, false); //true any ca
            curl_setopt(self::$curlHandler, CURLOPT_SSL_VERIFYHOST, 1); //check only host
            self::setCurlSSLVersion($request);
        }
        $ret = curl_exec(self::$curlHandler);
        self::$httpInfo = curl_getinfo(self::$curlHandler);

        return $ret;
    }

    public static function info()
    {
        return self::$httpInfo;
    }

    private static function setCurlSSLVersion($request)
    {
        if (isset($request['ssl_version'])) {
            curl_setopt(self::$curlHandler, CURLOPT_SSLVERSION, $request['ssl_version']);
        } else {
            curl_setopt(self::$curlHandler, CURLOPT_SSLVERSION, 4);
        }
    }
}
