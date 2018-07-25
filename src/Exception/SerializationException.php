<?php
namespace OneOffTech\GeoServer\Exception;

class SerializationException extends GeoServerClientException
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
