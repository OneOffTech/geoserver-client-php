# Supported file formats

The library is able to recognize:

* `Shapefile`
* `Shapefile` inside `zip` archive
* `GeoTIFF`
* `GeoPackage` format version 1.2 (can contain both vector and raster data, but will be reported as vector)
* Styled Layer Descriptor `SLD` files for layer styles in XML format

You can check if a file is supported using

```php
use OneOffTech\GeoServer\GeoFile;
use OneOffTech\GeoServer\StyleFile;

$isSupported = GeoFile::isSupported($path);
// => true/false


// For style files, the support is available with the StyleFile class
$isSupported = StyleFile::isSupported($path);
// => true/false
```

> The library supports only the file formats that can be uploaded to a GeoServer. 
> For example `Geojson`, `KML` and `GPX` are not supported out-of-the-box by GeoServer,
> although plugins might be available for doing that

# File Character Encoding

GeoServer can deal with character encoding of layers/features, but tests highlighted
a limited support when using UTF-8 for data/coverage store names.

To prevent issues with UTF-8 features attributes, the client set the character encoding
to UTF-8 when uploading a file. In this way GeoServer will consider UTF-8 as the
character encoding for features/attributes contained in the file. This do not affect
layer or store names.

As by design decision the store name is the filename, or the assigned name when 
performing the upload, we highly reccomend to use ASCII characters for it.
Use of UTF-8 encoded filenames (or store name) might prevent the client to retrieve the 
store/layer corresponding to the uploaded file.