[![Build Status](https://travis-ci.org/OneOffTech/geoserver-client-php.svg?branch=master)](https://travis-ci.org/OneOffTech/geoserver-client-php)

# GeoServer PHP Client

The client enables programmatic access to a [GeoServer](http://geoserver.org/) instance.

As of now the following features will be supported:

* [x] Obtain the version of the connected GeoServer instance
* [ ] Create datastores
* [ ] List existing datastores

> **This package is being actively developed and is not ready for production**

**Requirements**

- [PHP 7.1](http://www.php.net/) or above.
- [GeoServer](http://geoserver.org/) 2.13.0 or above

## Getting Started

### Installation

The GeoServer client uses [Composer](http://getcomposer.org/) to manage its dependencies. 

```bash
composer require php-http/guzzle6-adapter guzzlehttp/psr7 oneofftech/geoserver-client-php
```

The GeoServer client is not hard coupled to [Guzzle](https://github.com/guzzle/guzzle) or any other library that sends HTTP messages. It uses an abstraction called [HTTPlug](http://httplug.io/). This will give you the flexibilty to choose what PSR-7 implementation and HTTP client to use.



**Why requiring so many packages?**

GeoServer client has a dependency on the virtual package
[php-http/client-implementation](https://packagist.org/providers/php-http/client-implementation) which requires to you install **an** adapter, but we do not care which one. That is an implementation detail in your application. We also need **a** PSR-7 implementation and **a** message factory. 

You do not have to use the `php-http/guzzle6-adapter` if you do not want to. You may use the `php-http/curl-client`. Read more about the virtual packages, why this is a good idea and about the flexibility it brings at the [HTTPlug docs](http://docs.php-http.org/en/latest/httplug/users.html).

### Usage

...

## Testing

The library is covered with unit and integration tests. 

```
vendor/bin/phpunit
```

By default integration tests are not executed unless in the phpunit.xml file a GeoServer instance is specified.

The `phpunit.xml.dist` define the `GEOSERVER_URL`, `GEOSERVER_USER`, `GEOSERVER_PASSWORD` for that purpose. 
If you want you can copy `phpunit.xml.dist` to `phpunit.xml` and edit those variables in place 
or define them in your environment variables.

```xml
<env name="GEOSERVER_URL" value="http://localhost:8600/geoserver/"/>
<env name="GEOSERVER_USER" value="user"/>
<env name="GEOSERVER_PASSWORD" value="pass"/>
```

If you don't have a GeoServer instance to trash there is a `docker-compose.yml` file that, with 
the help of Docker and the [kartoza/geoserver image](https://hub.docker.com/r/kartoza/geoserver/), 
creates a running GeoServer instance on port 8600.

> Be aware that the Kartoza GeoServer image requires [4GB of RAM](https://github.com/kartoza/docker-geoserver/blob/master/Dockerfile#L23-L25) to run

```bash
docker-compose -f ./tests/docker-compose.yml up -d
# here better to wait for the full startup of the geoserver
vendor/bin/phpunit
```

## Contributing

Hey, we're accepting Pull Requests. Please target your pull request to the `master` branch.

## License

This project is licensed under the AGPL v3 license, see [LICENSE.txt](./LICENSE.txt).
