[![Build Status](https://travis-ci.org/OneOffTech/geoserver-client-php.svg?branch=master)](https://travis-ci.org/OneOffTech/geoserver-client-php)

# GeoServer PHP Client

The client enables programmatic access to a [GeoServer](http://geoserver.org/) instance.

As of now it offers the following features:

* [x] Obtain the version of the connected GeoServer instance
* [x] Create workspace
* [x] Retrieve workspace details
* [x] List existing datastores
* [x] Create datastores
* [x] Create coveragestores
* [x] Upload files
* [x] Retrieve uploaded files

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

#### Creating a client

To create a GeoServer client use the `GeoServer::build` method. 
It will give you back a configured client instance.

As of now the client can handle 1 workspace at time by design.

```php
use OneOffTech\GeoServer\GeoServer;
use OneOffTech\GeoServer\Auth\Authentication;

/**
 * Define a workspace to use
 */
$workspace = 'your-workspace';

$authentication = new Authentication('geoserver_user', 'geoserver_password');

$geoserver = GeoServer::build($url, $workspace, $authentication);
```

#### Get the GeoServer version

Once you have a client instance, you can verify the GeoServer version number using `version()`

```php
// assuming to have a GeoServer instance in the $geoserver variable
$version = $geoserver->version();
// => 2.13.0
```

#### Create the workspace

The client can create the configured workspace if not available. 
To do so call `createWorkspace()`:

```php
$workspace = $geoserver->createWorkspace();
// => \OneOffTech\GeoServer\Models\Workspace
```

In case the creation goes well or the workspace already exists, a `Workspace` instance is returned.

#### Retrieve the workspace details

The `workspace()` method retrieve the details of the configured workspace

```php
$workspace = $geoserver->workspace();
// => \OneOffTech\GeoServer\Models\Workspace
```


#### Datastores

A datastore is a container of vector data. A workspace can have multiple data stores.

You can retrieve all defined datastores using the `datastores()` method:

```php
$datastores = $geoserver->datastores();
// => array of \OneOffTech\GeoServer\Models\DataStore
```

Or retrieve a datastores by name:

```php
$datastore = $geoserver->datastore($name);
// => \OneOffTech\GeoServer\Models\DataStore
```


#### Uploading a Shapefile to a workspace

Uploading a file to a geoserver instance is done via the `upload` method.

The client recognizes the format and create a correct store type, e.g. shapefiles lead to a 
datastore creation. To do so the file path must be wrapped in a `GeoFile` object.

```php
use OneOffTech\GeoServer\GeoFile;

$file = GeoFile::load('path/to/shapefile.shp');
// => OneOffTech\GeoServer\GeoFile{
//     + mimeType
//     + extension
//     + format
//     + type
//     + name
//     + originalName
//    }
// it will throw OneOffTech\GeoServer\Exception\UnsupportedFileException in case the file cannot be recognized
```

From a GeoFile instance the file `mimeType`, `format` and `type` (`vector` or `raster`, `OneOffTech\GeoServer\GeoType`) can be discovered.

The `format` is used to specify the content of the file, as in some cases Geographic files do not have a standard mime type. 
For example a Shapefile mime type is `application/octet-stream`, which means a binary file.

The `originalName` attribute contains the original filename and a `name` property. By default `originalName` and `name` are equals, 
but the name can be changed, by using the `name($value)` method. The `name` will be used as the store name inside GeoServer.

Once obtained a `GeoFile` instance, the method `upload()` can be used to really upload the file to the GeoServer:

```php
use OneOffTech\GeoServer\GeoFile;

$file = GeoFile::load('path/to/shapefile.shp');

$feature = $geoserver->upload($file);
// OneOffTech\GeoServer\Models\Feature
```

Once uploaded, the return value will be an instance of the `OneOffTech\GeoServer\Models\Feature`.
It contains the details extracted by the GeoServer, like the bounding box.

## Supported file formats

The library is able to recognize:

- `Shapefile`
- `Shapefile` inside `zip` archive
- `GeoTIFF`

You can check if a file is supported using

```php
use OneOffTech\GeoServer\GeoFile;

$isSupported = GeoFile::isSupported($path);
// => true/false
```

> The library supports only the file formats that can be uploaded to a GeoServer. 
> For example `Geojson`, `KML` and `GPX` are not supported out-of-the-box by GeoServer,
> although plugins might be available for doing that

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
