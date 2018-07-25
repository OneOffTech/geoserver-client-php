<?php
namespace OneOffTech\GeoServer\Http;

/**
 * @internal
 */
final class Routes
{
    /** @var string */
    private $baseUrl;

    /**
     * @param string $baseUrl
     */
    public function __construct($baseUrl)
    {
        $this->baseUrl = trim(trim($baseUrl), '/');
    }

    /**
     * @return string
     */
    public function url($endpoint)
    {
        return sprintf("%s/rest/%s", $this->baseUrl, $endpoint);
    }
}
