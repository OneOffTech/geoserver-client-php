<?php
namespace OneOffTech\GeoServer\Exception;

/**
 * Raised when a style cannot be uploaded as a previous style with the same name already exists
 */
class StyleAlreadyExistsException extends GeoServerClientException
{
    /**
     *
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message, 409);
    }


    public static function style($name, $workspace)
    {
        return new self("A style named [$name] already exists in [$workspace].");
    }
}
