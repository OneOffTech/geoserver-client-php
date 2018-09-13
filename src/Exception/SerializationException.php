<?php
namespace OneOffTech\GeoServer\Exception;

/**
 * Serialization exception
 * 
 * Thrown when an object cannot be serialized into JSON
 */
class SerializationException extends GeoServerClientException
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
