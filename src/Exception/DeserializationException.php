<?php
namespace OneOffTech\GeoServer\Exception;

/**
 * Deserialization exception
 *
 * Thrown when the JSON response from the server could not be deserialized into an object
 */
class DeserializationException extends GeoServerClientException
{
    /**
     *
     * @param string $message
     * @param string $json the original JSON that raised the deserialization error
     */
    public function __construct($message, $json)
    {
        parent::__construct("$message. $json");
    }
}
