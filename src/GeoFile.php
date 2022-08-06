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
 *
 */
class GeoFile
{
    protected $file;

    protected $name;
    
    protected $originalName;
    
    protected $mimeType;

    /**
     * The geodata format
     */
    protected $format;
    
    protected $extension;
    
    /**
     * The extension as required by GeoServer
     *
     * e.g. for a geo tiff file the extension must be .geotiff
     */
    protected $normalizedExtension;

    /**
     * The mime type as required by GeoServer
     *
     * e.g. for a geo tiff file the mime type appears to be "geotif/geotiff",
     * as found in https://gis.stackexchange.com/questions/218162/creating-coveragestore-geotiff-using-rest-api
     */
    protected $normalizedMimeType;

    /**
     * The type of the geodata (vector or raster)
     */
    protected $type;

    public function __construct($path)
    {
        $this->file = new SplFileInfo($path);

        list($format, $type, $mimeType) = TypeResolver::identify($path);

        if (! in_array($format, TypeResolver::supportedFormats())) {
            throw new UnsupportedFileException($path, $format, join(', ', TypeResolver::supportedFormats()));
        }

        $this->mimeType = $mimeType;
        
        $this->extension = $this->file->getExtension();
        
        $this->normalizedExtension = TypeResolver::normalizedExtensionFromFormat($format) ?? $this->extension;
        
        $this->normalizedMimeType = TypeResolver::normalizedMimeTypeFromFormat($format) ?? $mimeType;

        $this->format = $format;
        
        $this->type = $type;

        $this->name = $this->originalName = $this->file->getFileName();
    }

    /**
     * Set the data name.
     * It will be used for store name
     *
     * @param string $value
     * @return Data
     */
    public function name($value)
    {
        $this->name = $value;

        return $this;
    }

    /**
     * Tell if the name attribute is different from the original filename
     *
     * @return bool
     */
    public function wasRenamed()
    {
        return $this->name !== $this->originalName;
    }

    /**
     * Get the path to the file
     *
     * @return string
     */
    public function path()
    {
        return $this->file->getRealPath();
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
     * Copy the GeoFile content into a temporary folder and return the new GeoFile instance
     *
     * Please note that the temporary file is not disposed automatically
     *
     * @param string $temporaryFolder
     * @return GeoFile
     */
    public function copy($temporaryFolder = null)
    {
        $tmpfilename = tempnam($temporaryFolder ?? sys_get_temp_dir(), $this->name);
        $handle = fopen($tmpfilename, "w+b");
        fwrite($handle, $this->content());
        fclose($handle);

        return GeoFile::from($tmpfilename)->name($this->name);
    }

    /**
     * Create a Geo file instance from a given file path
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
     * Check if the specified file is a valid geographical file
     *
     * @param string $path
     * @return bool
     */
    public static function isSupported(string $path)
    {
        list($format, $type, $mimeType) = TypeResolver::identify($path);
        return in_array($format, TypeResolver::supportedFormats());
    }
}
