<?php

namespace OneOffTech\GeoServer\Exception;

/**
 * Error response Exception
 * 
 * Thrown when the server respond with a failure
 */
class ErrorResponseException extends GeoServerClientException
{
    /**
     * @var mixed
     */
    private $data;

    /**
     * ErrorResponseException constructor.
     *
     * @param string $message
     * @param int $code
     * @param mixed $data
     */
    public function __construct($message, $code, $data)
    {
        parent::__construct($message, $code);
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
