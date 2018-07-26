<?php
namespace Tests\Integration;

use Tests\TestCase;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use OneOffTech\GeoServer\Exception\ErrorResponseException;
use OneOffTech\GeoServer\Exception\InvalidDataException;
use Tests\Concern\SetupIntegrationTest;
use OneOffTech\GeoServer\Models\Workspace;
use OneOffTech\GeoServer\Models\DataStore;

class GeoServerDataStoresTest extends TestCase
{
    use SetupIntegrationTest;

    public function test_datastores_are_retrieved()
    {
        $datastores = $this->geoserver->datastores();

        $this->assertContainsOnlyInstancesOf(DataStore::class, $datastores);
    }

    public function test_datastore_can_be_retrieved_by_name()
    {
        $datastore = $this->geoserver->datastore('test');

        $this->assertInstanceOf(DataStore::class, $datastore);
        $this->assertEquals(getenv('GEOSERVER_WORKSPACE'), $datastore->workspace);
        $this->assertEmpty($datastore->href);
        $this->assertNotEmpty($datastore->featureTypes);
        $this->assertNotEmpty($datastore->connectionParameters);
        $this->assertTrue($datastore->enabled);
    }
}
