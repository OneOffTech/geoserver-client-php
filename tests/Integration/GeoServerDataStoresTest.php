<?php
namespace Tests\Integration;

use Tests\TestCase;
use GuzzleHttp\Psr7\Request;
use OneOffTech\GeoServer\GeoFile;
use Psr\Http\Message\RequestInterface;
use OneOffTech\GeoServer\Exception\ErrorResponseException;
use OneOffTech\GeoServer\Exception\InvalidDataException;
use Tests\Concern\SetupIntegrationTest;
use OneOffTech\GeoServer\Models\Workspace;
use OneOffTech\GeoServer\Models\DataStore;
use OneOffTech\GeoServer\Models\Feature;
use OneOffTech\GeoServer\Exception\StoreNotFoundException;

class GeoServerDataStoresTest extends TestCase
{
    use SetupIntegrationTest;

    public function test_shapefile_can_be_uploaded()
    {
        $datastoreName = 'shapefile_test';
        $data = GeoFile::from(__DIR__ . '/../fixtures/shapefile.shp')->name($datastoreName);

        $feature = $this->geoserver->upload($data);

        $this->assertInstanceOf(Feature::class, $feature);
        $this->assertEquals("shapefile_test", $feature->name);
        $this->assertEquals("shapefile_test", $feature->title);
        $this->assertEquals("shapefile_test", $feature->nativeName);
        $this->assertEquals("EPSG:404000", $feature->srs);
        $this->assertTrue($feature->enabled);
        $this->assertFalse($feature->overridingServiceSRS);
        $this->assertFalse($feature->skipNumberMatched);
        $this->assertFalse($feature->circularArcPresent);
        $this->assertNotNull($feature->store);
        $this->assertNotNull($feature->keywords);
        $this->assertNotNull($feature->nativeBoundingBox);
        $this->assertNotNull($feature->boundingBox);
        $this->assertEquals(0.0, $feature->nativeBoundingBox->minX);
        $this->assertEquals(0.0, $feature->nativeBoundingBox->minY);
        $this->assertEquals(-1.0, $feature->nativeBoundingBox->maxX);
        $this->assertEquals(-1.0, $feature->nativeBoundingBox->maxY);

        return $datastoreName;
    }

    /**
     * @depends test_shapefile_can_be_uploaded
     */
    public function test_datastore_can_be_retrieved_by_name($datastoreName)
    {
        $datastore = $this->geoserver->datastore($datastoreName);

        $this->assertInstanceOf(DataStore::class, $datastore);
        $this->assertEquals(getenv('GEOSERVER_WORKSPACE'), $datastore->workspace);
        $this->assertEmpty($datastore->href);
        $this->assertNotEmpty($datastore->featureTypes);
        $this->assertNotEmpty($datastore->connectionParameters);
        $this->assertTrue($datastore->enabled);
        $this->assertTrue($datastore->exists);

        return $datastoreName;
    }

    /**
     * @depends test_datastore_can_be_retrieved_by_name
     */
    public function test_datastores_are_retrieved($datastoreName)
    {
        $datastores = $this->geoserver->datastores();

        $this->assertContainsOnlyInstancesOf(DataStore::class, $datastores);

        return $datastoreName;
    }

    /**
     * @depends test_datastores_are_retrieved
     */
    public function test_datastore_can_be_deleted($datastoreName)
    {
        $datastore = $this->geoserver->deleteDatastore($datastoreName);

        $this->assertInstanceOf(DataStore::class, $datastore);
        $this->assertEquals(getenv('GEOSERVER_WORKSPACE'), $datastore->workspace);
        $this->assertTrue($datastore->enabled);
        $this->assertFalse($datastore->exists);

        return $datastoreName;
    }

    public function test_non_existing_datastore_cannot_be_retrieved()
    {
        $this->expectException(StoreNotFoundException::class);

        $datastore = $this->geoserver->datastore('some_name');
    }
}
