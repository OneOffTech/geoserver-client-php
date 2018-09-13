<?php
namespace Tests\Integration;

use Tests\TestCase;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Tests\Concern\SetupIntegrationTest;
use OneOffTech\GeoServer\Exception\InvalidDataException;
use OneOffTech\GeoServer\Exception\ErrorResponseException;

class GeoServerVersionRetrievalTest extends TestCase
{
    use SetupIntegrationTest;

    public function test_geoserver_version_is_retrieved()
    {
        $version = $this->geoserver->version();

        $this->assertNotEmpty($version);
        $this->assertTrue(is_string($version));
    }
}
