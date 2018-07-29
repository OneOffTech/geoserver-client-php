<?php

namespace OneOffTech\GeoServer\Support;

use OneOffTech\GeoServer\GeoType;
use OneOffTech\GeoServer\GeoFormat;
use JsonSchema\Validator as JsonSchemaValidator;

final class TypeResolver
{
    protected static $mimeTypes = [
        GeoFormat::SHAPEFILE => 'application/octet-stream', // shapefile
        GeoFormat::SHAPEFILE_ZIP => 'application/zip', // shapefile in ZIP container
        GeoFormat::GEOJSON => 'application/geo+json', // geojson
        GeoFormat::KML => 'application/vnd.google-earth.kml+xml', // Keyhole Markup Language
        GeoFormat::KMZ => 'application/vnd.google-earth.kmz', // KML in ZIP container
        GeoFormat::GPX => 'application/gpx+xml', // GPS eXchange Format
        GeoFormat::GEOTIFF => 'image/tiff', // geotiff
        // GeoFormat::GEOPACKAGE => '', // geopackage
    ];
    
    protected static $mimeTypeToFormat = [
        'application/geo+json' => GeoFormat::GEOJSON ,
        'application/vnd.google-earth.kml+xml' => GeoFormat::KML ,
        'application/vnd.google-earth.kmz' => GeoFormat::KMZ ,
        'application/gpx+xml' => GeoFormat::GPX ,
    ];

    protected static $typesMap = [
        GeoFormat::SHAPEFILE => GeoType::VECTOR,
        GeoFormat::SHAPEFILE_ZIP => GeoType::VECTOR,
        GeoFormat::GEOJSON => GeoType::VECTOR,
        GeoFormat::KML => GeoType::VECTOR,
        GeoFormat::KMZ => GeoType::VECTOR,
        GeoFormat::GPX => GeoType::VECTOR,
        GeoFormat::GEOTIFF => GeoType::RASTER,
        GeoFormat::GEOPACKAGE => GeoType::RASTER,

        GeoType::VECTOR => [
            GeoFormat::SHAPEFILE,
            GeoFormat::SHAPEFILE_ZIP,
            GeoFormat::GEOJSON,
            GeoFormat::KML,
            GeoFormat::KMZ,
            GeoFormat::GPX,
        ],
        GeoType::RASTER => [
            GeoFormat::GEOTIFF,
            GeoFormat::GEOPACKAGE,
        ]
    ];
    

    public static function identify($path)
    {
        $mimeType = mime_content_type($path);

        // try to recognize the format from the mime type.
        // this works for files that have a specific mime type
        $format = isset(self::$mimeTypeToFormat[$mimeType]) ? self::$mimeTypeToFormat[$mimeType] : null;
        
        // dump(compact('path', 'mimeType', 'format'));

        // for some files the mime type is too generic
        // so additional checks are required
        if ($mimeType === self::$mimeTypes[GeoFormat::SHAPEFILE]) {
            // According to https://www.esri.com/library/whitepapers/pdfs/shapefile.pdf
            // the first 4 bytes of a shapefile are always the number 9994
            $code = BinaryReader::readInt32($path);

            if ($code === 9994) {
                $format = GeoFormat::SHAPEFILE;
            }
        }

        if ($mimeType === 'application/json' || $mimeType === 'text/plain') {

            // check if is a GeoJSON, but enclosed in a plain json/text file
            $validator = new JsonSchemaValidator;
            $content = json_decode(file_get_contents($path));
            $schema = json_decode(file_get_contents(__DIR__ . '/../../schemas/geojson.json'));

            $result = $validator->validate($content, $schema);

            if ($validator->isValid()) {
                $format = GeoFormat::GEOJSON;
                $mimeType = self::$mimeTypes[GeoFormat::GEOJSON];
            }
        } elseif ($mimeType === 'application/zip') {

            // could be a KMZ or a compressed shapefile

            // Check if KMZ, By definition in https://developers.google.com/kml/documentation/kmzarchives
            // a KMZ contains only a main KML file that ends with .kml extension
            $containsKml = ZipReader::containsFile($path, '.kml');

            if ($containsKml) {
                $format = GeoFormat::KMZ;
                $mimeType = self::$mimeTypes[GeoFormat::KMZ];
            }
            
            // Check if the zip file contains at least 1 shapefile
            $containsShp = ZipReader::containsFile($path, '.shp');

            if ($containsShp) {
                $format = GeoFormat::SHAPEFILE_ZIP;
                $mimeType = self::$mimeTypes[GeoFormat::SHAPEFILE_ZIP];
            }
        } elseif ($mimeType === 'application/xml') {

            // could be KML or GPX

            // check if KML tag is present
            $data = join('', TextReader::readLines($path, 2));

            if (strpos($data, '<kml') !== false) {
                $format = GeoFormat::KML;
                $mimeType = self::$mimeTypes[GeoFormat::KML];
            } elseif (strpos($data, '<gpx') !== false) {
                // http://www.topografix.com/gpx/1/1/gpx.xsd
                $format = GeoFormat::GPX;
                $mimeType = self::$mimeTypes[GeoFormat::GPX];
            }
        } elseif ($mimeType === 'image/tiff' && BinaryReader::isGeoTiff($path)) {
            $format = GeoFormat::GEOTIFF;
        }
        

        $type = self::convertFormatToType($format);
    
        return [
            $format,
            $type,
            $mimeType
        ];
    }

    public static function supportedMimeTypes()
    {
        return array_value(static::$mimeTypes);
    }
    
    public static function supportedFormats()
    {
        return array_keys(static::$mimeTypes);
    }

    public static function convertFormatToType($format)
    {
        return !is_null($format) && isset(static::$typesMap[$format]) ? static::$typesMap[$format] : null;
    }
}
