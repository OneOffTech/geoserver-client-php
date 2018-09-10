<?php

namespace OneOffTech\GeoServer\Support;

use Exception;
use Psr\Http\Message\ResponseInterface;

final class ImageResponse
{
    private $response = null;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function mimeType()
    {
        $contentTypeHeader = $this->response->getHeader('Content-Type');
        return $contentTypeHeader[0] ?? 'application/octet-stream';
    }
    
    public function asString()
    {
        return (string)$this->response->getBody();
    }

    public static function from(ResponseInterface $response)
    {
        return new static($response);
    }
}
