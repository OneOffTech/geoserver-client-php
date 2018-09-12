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
        GeoFormat::GEOTIFF => 'image/tiff', // geotiff
    ];
    
    protected static $mimeTypeToFormat = [];

    protected static $typesMap = [
        GeoFormat::SHAPEFILE => GeoType::VECTOR,
        GeoFormat::SHAPEFILE_ZIP => GeoType::VECTOR,
        GeoFormat::GEOTIFF => GeoType::RASTER,

        GeoType::VECTOR => [
            GeoFormat::SHAPEFILE,
            GeoFormat::SHAPEFILE_ZIP,
        ],
        GeoType::RASTER => [
            GeoFormat::GEOTIFF,
        ]
    ];

    /**
     * The file extension, given the file format, as accepted by GeoServer
     */
    protected static $normalizedFormatFileExtensions = [
        GeoFormat::SHAPEFILE => 'shp',
        GeoFormat::SHAPEFILE_ZIP => 'shp',
        GeoFormat::GEOTIFF => 'geotiff',
    ];

    /**
     * The file mime type, given the file format, as accepted by GeoServer
     */
    protected static $normalizedMimeTypeFileFormat = [
        GeoFormat::GEOTIFF => 'geotif/geotiff', // as found on https://gis.stackexchange.com/questions/218162/creating-coveragestore-geotiff-using-rest-api
        GeoFormat::SHAPEFILE_ZIP => 'application/zip',
    ];
    

    public static function identify($path)
    {
        $mimeType = mime_content_type($path);

        // try to recognize the format from the mime type.
        // this works for files that have a specific mime type
        $format = isset(self::$mimeTypeToFormat[$mimeType]) ? self::$mimeTypeToFormat[$mimeType] : null;
        
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

        if ($mimeType === 'application/zip') {

            // could be a compressed shapefile
            // Check if the zip file contains at least 1 shapefile
            $containsShp = ZipReader::containsFile($path, '.shp');

            if ($containsShp) {
                $format = GeoFormat::SHAPEFILE_ZIP;
                $mimeType = self::$mimeTypes[GeoFormat::SHAPEFILE_ZIP];
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

    /**
     * Get the GeoServer accepted file extension for the specific file format
     *
     * @return string|null The normalized extension or null in case no conversion is required
     */
    public static function normalizedExtensionFromFormat($format)
    {
        return !is_null($format) && isset(static::$normalizedFormatFileExtensions[$format]) ? static::$normalizedFormatFileExtensions[$format] : null;
    }

    /**
     * Get the GeoServer accepted file mime type for the specific file format
     *
     * @return string|null The normalized mime type or null in case no conversion is required
     */
    public static function normalizedMimeTypeFromFormat($format)
    {
        return !is_null($format) && isset(static::$normalizedMimeTypeFileFormat[$format]) ? static::$normalizedMimeTypeFileFormat[$format] : null;
    }
}
