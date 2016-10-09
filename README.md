# SOAP client using Curl

[![version][packagist-version]][packagist-url]
[![Downloads][packagist-downloads]][packagist-url]
[![Build Status][travis-status]][travis-url]

[packagist-url]: https://packagist.org/packages/aleplusplus/soapclient-curl-php
[packagist-license]: https://img.shields.io/packagist/l/aleplusplus/soapclient-curl-php.svg?style=flat
[packagist-version]: https://img.shields.io/packagist/v/aleplusplus/soapclient-curl-php.svg?style=flat
[packagist-downloads]: https://img.shields.io/packagist/dm/aleplusplus/soapclient-curl-php.svg?style=flat

[travis-status]: https://travis-ci.org/aleplusplus/soapclient-curl-php.svg?branch=master
[travis-url]: https://travis-ci.org/aleplusplus/soapclient-curl-php

## Install:

Via composer:

```
$ composer require aleplusplus/soapclient-curl-php
```

## Usage:

Using the `SoapClientCurl\SoapClientRequest`: in Server SOAP of SRI.

```php
use SoapClientCurl\SoapClientRequest;

// Clave Acceso
$claveAccesoComprobante = '<CLAVE_ACCESO>';

// Url Soap Server Example
$url = 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes';

$body = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ec="http://ec.gob.sri.ws.autorizacion">
            <soapenv:Header/>
            <soapenv:Body>
                <ec:autorizacionComprobante>
                        <claveAccesoComprobante>'.$claveAccesoComprobante.'</claveAccesoComprobante>
                </ec:autorizacionComprobante>
            </soapenv:Body>
        </soapenv:Envelope>';

$headers = array('Content-Type: text/xml; charset=utf-8', 'Content-Length: '.strlen($body));

$result = SoapClientRequest::send($url, $headers, $body);
```