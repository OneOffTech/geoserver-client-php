<?php
namespace OneOffTech\GeoServer\Exception;

/**
 * Raised when a style cannot be found on the specific geoserver instance
 */
class StyleNotFoundException extends GeoServerClientException
{
    /**
     *
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message, 404);
    }

    /**
     * Create a style not found exception for a given style
     *
     * @param string $name the style name
     * @param string $workspace the workspace that was expecting to contain the style
     * @return StyleNotFoundException
     */
    public static function style($name, $workspace)
    {
        return new self("The style [$name] cannot be found in [$workspace].");
    }
}
