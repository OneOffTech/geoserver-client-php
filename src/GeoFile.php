<?php

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

        if (!in_array($format, TypeResolver::supportedFormats())) {
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

    public function content()
    {
        return file_get_contents($this->file->getRealPath());
    }


    public function __get($property)
    {
        return $this->$property;
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
