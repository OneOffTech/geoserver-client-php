# Installation

The GeoServer client uses [Composer](http://getcomposer.org/) to manage its dependencies. 

```bash
composer require php-http/guzzle7-adapter guzzlehttp/psr7 http-interop/http-factory-guzzle oneofftech/geoserver-client-php
```

The GeoServer client is not hard coupled to [Guzzle](https://github.com/guzzle/guzzle) or any other library that sends HTTP messages. It uses an abstraction called [HTTPlug](http://httplug.io/). This will give you the flexibilty to choose what PSR-7 implementation and HTTP client to use.

## Why requiring so many packages?

GeoServer client has a dependency on the virtual package
[php-http/client-implementation](https://packagist.org/providers/php-http/client-implementation) which requires to you install **an** adapter, but we do not care which one. That is an implementation detail in your application. We also need **a** PSR-7 implementation and **a** message factory.

You do not have to use the `php-http/guzzle7-adapter` if you do not want to. You may use the `php-http/curl-client`. Read more about the virtual packages, why this is a good idea and about the flexibility it brings at the [HTTPlug docs](http://docs.php-http.org/en/latest/httplug/users.html).
