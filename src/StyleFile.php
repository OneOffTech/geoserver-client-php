<?php

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

        if (!in_array($format, TypeResolver::supportedFormats())) {
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
