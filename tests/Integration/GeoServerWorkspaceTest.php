<?php
namespace Tests\Integration;

use Tests\TestCase;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use OneOffTech\GeoServer\Exception\ErrorResponseException;
use OneOffTech\GeoServer\Exception\InvalidDataException;
use Tests\Concern\SetupIntegrationTest;
use OneOffTech\GeoServer\Models\Workspace;

class GeoServerWorkspaceTest extends TestCase
{
    use SetupIntegrationTest;

    public function test_workspace_details_are_retrieved()
    {
        $workspace = $this->geoserver->workspace();

        $this->assertInstanceOf(Workspace::class, $workspace);
        $this->assertEquals(getenv('GEOSERVER_WORKSPACE'), $workspace->name);
        $this->assertNotEmpty($workspace->dataStores);
        $this->assertNotEmpty($workspace->coverageStores);
        $this->assertNotEmpty($workspace->wmsStores);
        $this->assertNotEmpty($workspace->wmtsStores);
    }
}
