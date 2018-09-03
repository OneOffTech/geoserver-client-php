<?php
namespace OneOffTech\GeoServer\Exception;

/**
 * Raised when a store cannot be found on the specific geoserver instance
 */
class StoreNotFoundException extends GeoServerClientException
{
    /**
     *
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message, 404);
    }


    public static function datastore($name)
    {
        return new self("Data store [$name] not found.");
    }
    
    public static function coveragestore($name)
    {
        return new self("Coverage store [$name] not found.");
    }
}
