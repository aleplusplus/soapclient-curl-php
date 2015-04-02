<?php

namespace SoapClientCurl;

class SoapClientRequest {

    private static $handle = null;
    private static $socketTimeout = null;
    private static $defaultHeaders = array();

    private static $auth = array (
        'user' => '',
        'pass' => '',
        'method' => CURLAUTH_BASIC
    );
    private static $proxy = array(
        'port' => false,
        'tunnel' => false,
        'address' => false,
        'type' => CURLPROXY_HTTP,
        'auth' => array (
            'user' => '',
            'pass' => '',
            'method' => CURLAUTH_BASIC
        )
    );

    /**
     * Send a cURL request
     * @param string $url URL to send the request to
     * @param mixed $body request body
     * @param array $headers additional headers to send
     * @param string $username Authentication username (deprecated)
     * @param string $password Authentication password (deprecated)
     * @return SoapClientResponse
     * @throws \Exception if a cURL error occurs
     */
    public static function send($url, $headers = array(), $body = null, $username = null, $password = null)
    {
        self::$handle = curl_init();

        curl_setopt_array(self::$handle, array(
            CURLOPT_URL => self::encodeUrl($url),
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => self::getFormattedHeaders($headers),
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_POST => true,
            CURLOPT_VERBOSE => true,
            CURLOPT_HEADER => true
        ));
        if (self::$socketTimeout !== null) {
            curl_setopt(self::$handle, CURLOPT_TIMEOUT, self::$socketTimeout);
        }
        // supporting deprecated http auth method
        if (!empty($username)) {
            curl_setopt_array(self::$handle, array(
                CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                CURLOPT_USERPWD => $username . ':' . $password
            ));
        }
        if (!empty(self::$auth['user'])) {
            curl_setopt_array(self::$handle, array(
                CURLOPT_HTTPAUTH    => self::$auth['method'],
                CURLOPT_USERPWD     => self::$auth['user'] . ':' . self::$auth['pass']
            ));
        }
        if (self::$proxy['address'] !== false) {
            curl_setopt_array(self::$handle, array(
                CURLOPT_PROXYTYPE       => self::$proxy['type'],
                CURLOPT_PROXY           => self::$proxy['address'],
                CURLOPT_PROXYPORT       => self::$proxy['port'],
                CURLOPT_HTTPPROXYTUNNEL => self::$proxy['tunnel'],
                CURLOPT_PROXYAUTH       => self::$proxy['auth']['method'],
                CURLOPT_PROXYUSERPWD    => self::$proxy['auth']['user'] . ':' . self::$proxy['auth']['pass']
            ));
        }
        $response   = curl_exec(self::$handle);
        $error      = curl_error(self::$handle);
        $info       = self::getInfo();
        if ($error) {
            throw new \Exception($error);
        }
        // Split the full response in its headers and body
        $header_size = $info['header_size'];
        $header      = substr($response, 0, $header_size);
        $body        = substr($response, $header_size);

        return new SoapClientResponse($info, $header, $body);
    }

    public static function getInfo()
    {
        return curl_getinfo(self::$handle);
    }
    public static function getCurlHandle()
    {
        return self::$handle;
    }
    public static function getFormattedHeaders($headers)
    {
        $formattedHeaders = array();
        $combinedHeaders = array_change_key_case(array_merge((array) $headers, self::$defaultHeaders));
        foreach ($combinedHeaders as $key => $val) {
            $formattedHeaders[] = $val;
        }
        if (!array_key_exists('user-agent', $combinedHeaders)) {
            $formattedHeaders[] = 'user-agent: soapclient-request/1.0';
        }
        if (!array_key_exists('expect', $combinedHeaders)) {
            $formattedHeaders[] = 'expect:';
        }
        return $formattedHeaders;
    }
    private static function getArrayFromQuerystring($query)
    {
        $query = preg_replace_callback('/(?:^|(?<=&))[^=[]+/', function ($match) {
            return bin2hex(urldecode($match[0]));
        }, $query);
        parse_str($query, $values);
        return array_combine(array_map('hex2bin', array_keys($values)), $values);
    }

    /**
     * Ensure that a URL is encoded and safe to use with cURL
     * @param  string $url URL to encode
     * @return string
     */
    private static function encodeUrl($url)
    {
        $url_parsed = parse_url($url);
        $scheme = $url_parsed['scheme'] . '://';
        $host   = $url_parsed['host'];
        $port   = (isset($url_parsed['port']) ? $url_parsed['port'] : null);
        $path   = (isset($url_parsed['path']) ? $url_parsed['path'] : null);
        $query  = (isset($url_parsed['query']) ? $url_parsed['query'] : null);
        if ($query !== null) {
            $query = '?' . http_build_query(self::getArrayFromQuerystring($query));
        }
        if ($port && $port[0] !== ':') {
            $port = ':' . $port;
        }
        $result = $scheme . $host . $port . $path . $query;
        return $result;
    }
}
