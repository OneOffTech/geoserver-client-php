<?php
namespace OneOffTech\GeoServer\Http;

use OneOffTech\GeoServer\Support\WmsOptions;

/**
 * Helper class for managing URL creation
 *
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
     * Helper for creating GeoServer Rest URLs
     *
     * @param string $endpoint the endpoint to attach to the base URL
     * @return string
     */
    public function url($endpoint)
    {
        return sprintf("%s/rest/%s", $this->baseUrl, $endpoint);
    }
    
    /**
     * Web Map Service (WMS) service url helper
     *
     * Create the URL to the WMS service based on the specified options
     *
     * @param string $workspace The workspace the URL will refer to
     * @param \OneOffTech\GeoServer\Support\WmsOptions $options The WMS service options
     * @return string
     */
    public function wms($workspace, WmsOptions $options)
    {
        return sprintf(
            "%s/%s/wms?service=WMS&%s",
            $this->baseUrl,
            $workspace,
            $options->toUrlParameters()
        );
    }
}
