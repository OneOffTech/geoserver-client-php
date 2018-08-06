<?php
namespace OneOffTech\GeoServer\Exception;

class UnsupportedFileException extends GeoServerClientException
{
    /**
     * @param string $path
     * @param string $format
     * @param string $supportedFormats
     */
    public function __construct($path, $format, $supportedFormats)
    {
        parent::__construct("The given file [$path] is not supported. Found [$format] expected [$supportedFormats]");
    }
}
