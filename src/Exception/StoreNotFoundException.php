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


    /**
     * Create a store not found exception for a data store
     *
     * @param string $name the data store name
     * @return StoreNotFoundException
     */
    public static function datastore($name)
    {
        return new self("Data store [$name] not found.");
    }
    
    /**
     * Create a store not found exception for a coverage store
     *
     * @param string $name the coverage store name
     * @return StoreNotFoundException
     */
    public static function coveragestore($name)
    {
        return new self("Coverage store [$name] not found.");
    }
}
