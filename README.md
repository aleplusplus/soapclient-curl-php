# SOAP client using Curl

[![Packagist][packagist-version]][packagist-url]
[![Downloads][packagist-downloads]][packagist-url]
[![Build Status][travis-status]][travis-url]

[packagist-url]: https://packagist.org/packages/aleplusplus/soapclient-curl-php
[packagist-version]: https://img.shields.io/packagist/v/aleplusplus/soapclient-curl-php.svg?style=flat
[packagist-downloads]: https://img.shields.io/packagist/dm/aleplusplus/soapclient-curl-php.svg?style=flat

[travis-status]: https://travis-ci.org/aleplusplus/soapclient-curl-php.svg?branch=master
[travis-url]: https://travis-ci.org/aleplusplus/soapclient-curl-php

## Install:

Via composer:

```
$ composer require aleplusplus/soapclient-curl-php
```

## Example:

Using the `SoapClientCurl\SoapClientRequest` in SOAP Server of SRI:

```php
use SoapClientCurl\SoapClientRequest;

// Url Soap Server Example
$url = '<SOAP_SERVER_URL>';

$body = '<SOAP_SCHEMA>';

$headers = array('Content-Type: text/xml; charset=utf-8', 'Content-Length: '.strlen($body));

$result = SoapClientRequest::send($url, $headers, $body);

print_r($result);
```

For more detail see [test](https://github.com/aleplusplus/soapclient-curl-php/blob/master/tests/SoapClientRequestTest.php).
