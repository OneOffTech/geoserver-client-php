<?php
namespace Tests\Integration;

use Tests\TestCase;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use OneOffTech\GeoServer\Exception\ErrorResponseException;
use OneOffTech\GeoServer\Exception\InvalidDataException;
use Tests\Concern\SetupIntegrationTest;

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
