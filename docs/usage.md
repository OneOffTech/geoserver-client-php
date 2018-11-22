
# Usage

## Creating a client

To create a GeoServer client use the `GeoServer::build` method. 
It will give you back a configured client instance.

As of now the client can handle 1 workspace at time by design.

```php
use OneOffTech\GeoServer\GeoServer;
use OneOffTech\GeoServer\Auth\Authentication;

/**
 * Geoserver URL
 */
$url = 'http://localhost:8600/geoserver/';

/**
 * Define a workspace to use
 */
$workspace = 'your-workspace';

$authentication = new Authentication('geoserver_user', 'geoserver_password');

$geoserver = GeoServer::build($url, $workspace, $authentication);
```

## Get the GeoServer version

Once you have a client instance, you can verify the GeoServer version number using `version()`

```php
// assuming to have a GeoServer instance in the $geoserver variable
$version = $geoserver->version();
// => 2.13.0
```

## Create the workspace

The client can create the configured workspace if not available. 
To do so call `createWorkspace()`:

```php
$workspace = $geoserver->createWorkspace();
// => \OneOffTech\GeoServer\Models\Workspace
```

In case the creation goes well or the workspace already exists, a `Workspace` instance is returned.

## Retrieve the workspace details

The `workspace()` method retrieve the details of the configured workspace

```php
$workspace = $geoserver->workspace();
// => \OneOffTech\GeoServer\Models\Workspace
```


## Data stores

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

You cal also delete a data store via:

```php
$result = $geoserver->deleteDatastore($name);
// => true || false
```

## Coverage stores

A coveragestore is a container of raster data. A workspace can have multiple coverage stores.

You can retrieve all defined coverage stores using the `coveragestores()` method:

```php
$coveragestores = $geoserver->coveragestores();
// => array of \OneOffTech\GeoServer\Models\CoverageStore
```

Or retrieve a coveragestores by name:

```php
$coveragestore = $geoserver->coveragestore($name);
// => \OneOffTech\GeoServer\Models\CoverageStore
```

You cal also delete a coverage store via:

```php
$result = $geoserver->deleteCoveragestore($name);
// => true || false
```

## Upload and Delete geographic files

### Uploading geographic files

Uploading a file to a GeoServer instance is done via the `upload` method.

The client recognizes the format and create a correct store type, e.g. shapefiles lead to a 
datastore creation. To do so the file path must be wrapped in a `GeoFile` object.

> See [Supported files](./supported-files.md) for knowing what the library can handle

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

The `originalName` attribute contains the original filename. By default `originalName` and `name` are equals, 
but the name can be changed, by using the `name($value)` method. The `name` will be used as the store name inside GeoServer.

Once obtained a `GeoFile` instance, the method `upload()` can be used to really upload the file to the GeoServer:

```php
use OneOffTech\GeoServer\GeoFile;

$file = GeoFile::load('path/to/shapefile.shp');

$feature = $geoserver->upload($file);
// OneOffTech\GeoServer\Models\Resource
```

> For file character encoding please refer to [File Character Encoding](./supported-files.md#file-character-encoding)

Once uploaded, the return value will be an instance of the `OneOffTech\GeoServer\Models\Resource`.
It contains the details extracted by the GeoServer, like the bounding box.

`OneOffTech\GeoServer\Models\Resource` has two sub-classes:

- `OneOffTech\GeoServer\Models\Feature` A feature type is a vector based spatial resource or data set that originates from a data store 
- `OneOffTech\GeoServer\Models\Coverage` A coverage is a raster data set which originates from a coverage store.

### Verify if a geographic files exists

As a helper method, given a `GeoFile` instance, is possible to verify that a corresponding Feature or Coverage is present.

```php
use OneOffTech\GeoServer\GeoFile;

$file = GeoFile::load('path/to/shapefile.shp');

$exists = $geoserver->exist($file);
// true || false
```

> The identification currently uses the name assigned to the GeoFile

### Delete a geographic file

As a helper method, given a `GeoFile` instance, is possible to delete the corresponding Feature or Coverage in the Geoserver.

```php
use OneOffTech\GeoServer\GeoFile;

$file = GeoFile::load('path/to/shapefile.shp');

$removed = $geoserver->remove($file);
// true || false
```

> The identification currently uses the name assigned to the GeoFile

## Styles

The client can upload, retrieve and delete styles defined within the configured workspace

### Upload style file

To upload a style, a `StyleFile` instance representing the file on disk is required.
Once the instance is obtained use the `uploadStyle` method on a GeoServer client instance.

The style will be uploaded as part of the workspace styles.

```php
use OneOffTech\GeoServer\StyleFile;

$file = StyleFile::from('/path/to/style.sld');
// => OneOffTech\GeoServer\StyleFile{
//     + name
//     + originalName
//     + mimeType
//     + extension
//    }
// it will throw OneOffTech\GeoServer\Exception\UnsupportedFileException in case the file cannot be recognized

// You can change the style name before uploading it to avoid collision. By default the filename will be used.
$file->name('my_custom_style');

$style = $geoserver->uploadStyle($file);
// => OneOffTech\GeoServer\Models\Style
```

### Retrieve a style

The client let you retrieve a style by its name

```php
$style = $geoserver->style('style_name');
// => OneOffTech\GeoServer\Models\Style
```

> The name must be equal to the one given for the upload.
> It might not be the file name

### Retrieve all styles

You can also retrieve all styles defined in the workspace

```php
$styles = $geoserver->styles();
// => array of OneOffTech\GeoServer\Models\Style
```

### Remove a style

Style removal is performed by giving the style name to the `removeStyle` method. 
The method will return the details of the deleted style.

```php
$style = $geoserver->removeStyle('style_name');
// => OneOffTech\GeoServer\Models\Style
```

> the `$style->exists` attribute will be set to `false` after deletion
