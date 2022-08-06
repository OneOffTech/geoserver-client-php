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

namespace OneOffTech\GeoServer;

use SplFileInfo;
use OneOffTech\GeoServer\Support\TypeResolver;
use OneOffTech\GeoServer\Exception\UnsupportedFileException;

/**
 * A file that contains rendering style for a Web Map Service
 */
class StyleFile
{
    const MIME_TYPE = "application/vnd.ogc.sld+xml";

    protected $file;

    protected $name;
    
    protected $originalName;
    
    protected $mimeType;
    
    protected $extension;

    /**
     * The mime type as required by GeoServer
     *
     * e.g. for a geo tiff file the mime type appears to be "geotif/geotiff",
     * as found in https://gis.stackexchange.com/questions/218162/creating-coveragestore-geotiff-using-rest-api
     */
    protected $normalizedMimeType;

    public function __construct($path)
    {
        $this->file = new SplFileInfo($path);

        list($format, $type, $mimeType) = TypeResolver::identify($path);

        if (! in_array($format, TypeResolver::supportedFormats())) {
            throw new UnsupportedFileException($path, $format, join(', ', TypeResolver::supportedFormats()));
        }

        $this->mimeType = $mimeType;
        
        $this->extension = $this->file->getExtension();

        $this->normalizedMimeType = $mimeType;

        $this->originalName = $this->file->getFileName();
        $this->name = str_replace('.sld', '', $this->originalName);
    }

    /**
     * Set the style name.
     * It will be used when creating the style in the GeoServer
     *
     * @param string $value
     * @return StyleFile
     */
    public function name($value)
    {
        $this->name = $value;

        return $this;
    }

    public function content()
    {
        return file_get_contents($this->file->getRealPath());
    }

    public function __get($property)
    {
        return $this->$property;
    }

    /**
     * Create a StyleFile instance from a given file path
     *
     * @param string $path
     * @return Data
     * @throws UnsupportedFileException if file is not supported
     */
    public static function from($path)
    {
        return new static($path);
    }
    public static function load($path)
    {
        return static::from($path);
    }

    /**
     * Check if the specified file is a valid style file
     *
     * @param string $path
     * @return bool
     */
    public static function isSupported(string $path)
    {
        list($format, $type, $mimeType) = TypeResolver::identify($path);
        return $mimeType === self::MIME_TYPE;
    }
}
