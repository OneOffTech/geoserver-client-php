<?php
namespace OneOffTech\GeoServer\Http;

use OneOffTech\GeoServer\Support\WmsOptions;

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
     * Helper for creating GeoServer Rest URLs
     * @return string
     */
    public function url($endpoint)
    {
        return sprintf("%s/rest/%s", $this->baseUrl, $endpoint);
    }
    
    /**
     * Helper for creating Web Map Service (WMS) map urls for a layer
     * 
     * @return string
     */
    public function wms($workspace, WmsOptions $options)
    {
        $srs = $srs ?? 'EPSG:4326';

        return sprintf("%s/%s/wms?service=WMS&%s", 
            $this->baseUrl, $workspace, $options->toUrlParameters());

        // https://geoserver.test.oneofftech.xyz/geoserver/kbox/wms?service=WMS&version=1.1.0
        //     &request=GetMap
        //     &layers=kbox:Resource_Sites
        //     &styles=
        //     &bbox=-122.83676432991959,45.43253929856904,-122.47260028630856,45.65098261975053
        //     &width=768
        //     &height=460
        //     &srs=EPSG:4326
        //     &format=image%2Fpng
    }
}
