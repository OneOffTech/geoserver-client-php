<?php
namespace Tests\Concern;

use OneOffTech\GeoServer\GeoServer;
use OneOffTech\GeoServer\Auth\Authentication;

trait SetupIntegrationTest
{
    /**
     * @var OneOffTech\GeoServer\Client
     */
    protected $geoserver = null;
    
    protected function setUp()
    {
        parent::setUp();

        $url = getenv('GEOSERVER_URL');
        $workspace = getenv('GEOSERVER_WORKSPACE');
        
        if (empty($url)) {
            $this->markTestSkipped('The GEOSERVER_URL is not configured.');
        }
        
        $auth = new Authentication(getenv('GEOSERVER_USER'), getenv('GEOSERVER_PASSWORD'));

        $this->geoserver = GeoServer::build($url, $workspace, $auth);
    }
}
