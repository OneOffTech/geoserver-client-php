<?php
/*
 *    GeoServer PHP Client
 *
 *    Copyright (c) 2018 Oneoff-tech UG, Germany, www.oneofftech.xyz
 *
 *    This program is Free Software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public
 *    License along with this program.  If not, see
 *    <http://www.gnu.org/licenses/>.
 */

namespace OneOffTech\GeoServer\Support;

use OneOffTech\GeoServer\GeoType;
use OneOffTech\GeoServer\GeoFormat;

final class TypeResolver
{
    protected static $mimeTypes = [
        GeoFormat::SHAPEFILE => 'application/octet-stream', // shapefile
        GeoFormat::SHAPEFILE_ZIP => 'application/zip', // shapefile in ZIP container
        GeoFormat::GEOTIFF => 'image/tiff', // geotiff
        GeoFormat::SLD => 'application/vnd.ogc.sld+xml',
        GeoFormat::GEOPACKAGE => 'application/geopackage+sqlite3',
    ];
    
    protected static $mimeTypeToFormat = [];

    protected static $typesMap = [
        GeoFormat::SHAPEFILE => GeoType::VECTOR,
        GeoFormat::SHAPEFILE_ZIP => GeoType::VECTOR,
        GeoFormat::GEOTIFF => GeoType::RASTER,
        GeoFormat::GEOPACKAGE => GeoType::VECTOR,

        GeoType::VECTOR => [
            GeoFormat::SHAPEFILE,
            GeoFormat::SHAPEFILE_ZIP,
            GeoFormat::GEOPACKAGE,
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
        GeoFormat::GEOPACKAGE => 'gpkg',
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
        } elseif ($mimeType === 'application/xml' || $mimeType === 'text/xml') {

            // check if Style tag is present
            $data = join('', TextReader::readLines($path, 2));
            if (strpos($data, '<StyledLayerDescriptor') !== false) {
                $format = GeoFormat::SLD;
                $mimeType = self::$mimeTypes[GeoFormat::SLD];
            }
        } elseif (BinaryReader::isGeoPackage($path)) {
            $format = GeoFormat::GEOPACKAGE;
            $mimeType = self::$mimeTypes[GeoFormat::GEOPACKAGE];
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
        return ! is_null($format) && isset(static::$typesMap[$format]) ? static::$typesMap[$format] : null;
    }

    /**
     * Get the GeoServer accepted file extension for the specific file format
     *
     * @return string|null The normalized extension or null in case no conversion is required
     */
    public static function normalizedExtensionFromFormat($format)
    {
        return ! is_null($format) && isset(static::$normalizedFormatFileExtensions[$format]) ? static::$normalizedFormatFileExtensions[$format] : null;
    }

    /**
     * Get the GeoServer accepted file mime type for the specific file format
     *
     * @return string|null The normalized mime type or null in case no conversion is required
     */
    public static function normalizedMimeTypeFromFormat($format)
    {
        return ! is_null($format) && isset(static::$normalizedMimeTypeFileFormat[$format]) ? static::$normalizedMimeTypeFileFormat[$format] : null;
    }
}
