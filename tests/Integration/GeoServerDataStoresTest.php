<?php
namespace Tests\Integration;

use Tests\TestCase;
use GuzzleHttp\Psr7\Request;
use OneOffTech\GeoServer\GeoFile;
use OneOffTech\GeoServer\GeoType;
use Psr\Http\Message\RequestInterface;
use Tests\Concern\SetupIntegrationTest;
use OneOffTech\GeoServer\Models\Feature;
use OneOffTech\GeoServer\Models\DataStore;
use OneOffTech\GeoServer\Models\Workspace;
use OneOffTech\GeoServer\Exception\InvalidDataException;
use OneOffTech\GeoServer\Exception\ErrorResponseException;
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
        $this->assertEquals(GeoType::VECTOR, $feature->type());
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
        $this->assertEquals("EPSG:4326", $feature->boundingBox->crs);
        $this->assertEquals(314618.446, $feature->boundingBox->minX);
        $this->assertEquals(5536155.822, $feature->boundingBox->minY);
        $this->assertEquals(315358.647, $feature->boundingBox->maxX);
        $this->assertEquals(5536652.114, $feature->boundingBox->maxY);

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

    public function test_shapefile_can_be_uploaded_and_deleted()
    {
        $datastoreName = 'shapefile_test' . time();
        $data = GeoFile::from(__DIR__ . '/../fixtures/shapefile.shp')->name($datastoreName);

        $feature = $this->geoserver->upload($data);

        $this->assertInstanceOf(Feature::class, $feature);

        $this->assertTrue($this->geoserver->exist($data), "Data not existing after upload");
        
        $deleteResult = $this->geoserver->remove($data);

        $this->assertTrue($deleteResult, "GeoFile not deleted");

        $this->assertFalse($this->geoserver->exist($data), "Data still exists after remove");
    }

    public function test_shapefile_in_zip_archive_can_be_uploaded_and_deleted()
    {
        $datastoreName = 'buildings';
        $data = GeoFile::from(__DIR__ . '/../fixtures/buildings.zip')->name($datastoreName);

        $feature = $this->geoserver->upload($data);

        $this->assertInstanceOf(Feature::class, $feature);
        $this->assertEquals(GeoType::VECTOR, $feature->type());
        $this->assertEquals($datastoreName, $feature->name);
        $this->assertEquals($datastoreName, $feature->title);
        $this->assertEquals($datastoreName, $feature->nativeName);
        $this->assertEquals("EPSG:4326", $feature->srs);
        $this->assertTrue($feature->enabled);
        $this->assertFalse($feature->overridingServiceSRS);
        $this->assertFalse($feature->skipNumberMatched);
        $this->assertFalse($feature->circularArcPresent);
        $this->assertNotNull($feature->store);
        $this->assertNotNull($feature->keywords);
        $this->assertNotNull($feature->nativeBoundingBox);
        $this->assertNotNull($feature->boundingBox);
        $this->assertEquals("EPSG:4326", $feature->boundingBox->crs);
        $this->assertEquals(69.07695, $feature->boundingBox->minX);
        $this->assertEquals(34.41829, $feature->boundingBox->minY);
        $this->assertEquals(69.3082, $feature->boundingBox->maxX);
        $this->assertEquals(34.57558, $feature->boundingBox->maxY);

        // $this->assertTrue($this->geoserver->exist($data), "Data not existing after upload");
        
        // $deleteResult = $this->geoserver->remove($data);

        // $this->assertTrue($deleteResult, "GeoFile not deleted");

        // $this->assertFalse($this->geoserver->exist($data), "Data still exists after remove");
    }

    public function test_shapefile_in_zip_archive_can_be_renamed_during_upload_and_deleted()
    {
        $datastoreName = 'shapezipfile_test' . time();
        $data = GeoFile::from(__DIR__ . '/../fixtures/buildings.zip')->name($datastoreName);

        $feature = $this->geoserver->upload($data);

        $this->assertTrue(file_exists($data->path()), "File has been deleted");
        $this->assertInstanceOf(Feature::class, $feature);
        $this->assertEquals(GeoType::VECTOR, $feature->type());
        $this->assertEquals($datastoreName, $feature->name);
        $this->assertEquals($datastoreName, $feature->title);
        $this->assertEquals($datastoreName, $feature->nativeName);
        $this->assertEquals("EPSG:4326", $feature->srs);
        $this->assertTrue($feature->enabled);
        $this->assertFalse($feature->overridingServiceSRS);
        $this->assertFalse($feature->skipNumberMatched);
        $this->assertFalse($feature->circularArcPresent);
        $this->assertNotNull($feature->store);
        $this->assertNotNull($feature->keywords);
        $this->assertNotNull($feature->nativeBoundingBox);
        $this->assertNotNull($feature->boundingBox);
        $this->assertEquals("EPSG:4326", $feature->boundingBox->crs);
        $this->assertEquals(69.07695, $feature->boundingBox->minX);
        $this->assertEquals(34.41829, $feature->boundingBox->minY);
        $this->assertEquals(69.3082, $feature->boundingBox->maxX);
        $this->assertEquals(34.57558, $feature->boundingBox->maxY);

        $this->assertTrue($this->geoserver->exist($data), "Data not existing after upload");
        
        $deleteResult = $this->geoserver->remove($data);

        $this->assertTrue($deleteResult, "GeoFile not deleted");

        $this->assertFalse($this->geoserver->exist($data), "Data still exists after remove");
    }
}
