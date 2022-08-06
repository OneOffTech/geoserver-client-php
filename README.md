[![run-tests](https://github.com/OneOffTech/geoserver-client-php/actions/workflows/run-tests.yml/badge.svg)](https://github.com/OneOffTech/geoserver-client-php/actions/workflows/run-tests.yml)
![Packagist](https://img.shields.io/packagist/v/oneofftech/geoserver-client-php.svg)

# GeoServer PHP Client

This PHP library provides programmatic functions to access a [GeoServer](http://geoserver.org/).

It is Free and Open Source Software. All contributions are most welcome. Learn more about [how to contribute](./CONTRIBUTING.md).

#### Features

* Obtain the [version](./docs/usage.md#get-the-geoserver-version) of the connected GeoServer instance
* [Create workspace](./docs/usage.md#create-the-workspace) or retrieve existing workspace details
* [Create datastores](./docs/usage.md#data-stores) and listing them
* [Create coveragestores](./docs/usage.md#coverage-stores) and listing them
* [Upload files](./docs/usage.md#uploading-geographic-files) in various [formats](#supported-file-formats)
* [Manage styles](./docs/usage.md#styles) in SLD format

For detailed information of each of the provided functions check out the [documentation on the usage](./docs/usage.md).

#### Requirements

* [PHP 7.4](http://www.php.net/) or above.
* [GeoServer](http://geoserver.org/) 2.15.0 or above

## Installation

The GeoServer PHP Client uses [Composer](http://getcomposer.org/) to manage its dependencies.

```bash
composer require php-http/guzzle7-adapter guzzlehttp/psr7 http-interop/http-factory-guzzle oneofftech/geoserver-client-php
```

For more information, please review the [documentation on the installation process](./docs/installation.md).

## Supported file formats

The library handles:

* `Shapefile`
* `Shapefile` inside `zip` archive
* `GeoTIFF`
* `GeoPackage` format version 1.2 (can contain both vector and raster data, but will be reported as vector)
* Styled Layer Descriptor `SLD` files for layer styles in XML format

Read more on [supported file formats and encoding](./docs/supported-files.md).

## License

This project is Free and Open Source Software, licensed under the [AGPL v3 license](./LICENSE.txt).
