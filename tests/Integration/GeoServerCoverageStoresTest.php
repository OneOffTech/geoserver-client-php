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
use OneOffTech\GeoServer\Models\CoverageStore;
use OneOffTech\GeoServer\Models\Feature;
use OneOffTech\GeoServer\Models\Coverage;
use OneOffTech\GeoServer\Exception\StoreNotFoundException;

class GeoServerCoverageStoresTest extends TestCase
{
    use SetupIntegrationTest;

    public function test_geotiff_can_be_uploaded()
    {
        $storeName = 'geotiff_test';
        $data = GeoFile::from(__DIR__ . '/../fixtures/geotiff.tiff')->name($storeName);

        $feature = $this->geoserver->upload($data);

        $this->assertInstanceOf(Coverage::class, $feature);
        $this->assertEquals("geotiff_test", $feature->name);
        $this->assertEquals("geotiff_test", $feature->title);
        $this->assertEquals("geotiff_test", $feature->nativeName);
        $this->assertEquals("GeoTIFF", $feature->nativeFormat);
        $this->assertFalse($feature->skipNumberMatched);
        $this->assertFalse($feature->circularArcPresent);
        $this->assertNotNull($feature->store);
        $this->assertNotNull($feature->keywords);
        $this->assertNotNull($feature->nativeBoundingBox);
        $this->assertNotNull($feature->boundingBox);
        $this->assertNotEmpty($feature->interpolationMethods);
        $this->assertEquals(78999, $feature->nativeBoundingBox->minX);
        $this->assertEquals(1412948.0000000002, $feature->nativeBoundingBox->minY);
        $this->assertEquals(101839, $feature->nativeBoundingBox->maxX);
        $this->assertEquals(1439268.0000000002, $feature->nativeBoundingBox->maxY);
        $this->assertEquals(-83.64980947326015, $feature->latLonBoundingBox->minX);
        $this->assertEquals(42.724764597615966, $feature->latLonBoundingBox->minY);
        $this->assertEquals(-83.36533095896407, $feature->latLonBoundingBox->maxX);
        $this->assertEquals(42.96491963803106, $feature->latLonBoundingBox->maxY);
        $this->assertEquals("EPSG:4326", $feature->latLonBoundingBox->crs);

        return $storeName;
    }

    /**
     * @depends test_geotiff_can_be_uploaded
     */
    public function test_coveragestore_can_be_retrieved_by_name($coveragestoreName)
    {
        $coveragestore = $this->geoserver->coveragestore($coveragestoreName);

        $this->assertInstanceOf(CoverageStore::class, $coveragestore);
        $this->assertEquals(getenv('GEOSERVER_WORKSPACE'), $coveragestore->workspace);
        $this->assertEmpty($coveragestore->href);
        $this->assertEquals('file:data/test/geotiff_test/geotiff_test.geotiff', $coveragestore->url);
        $this->assertEquals('GeoTIFF', $coveragestore->type);
        $this->assertTrue($coveragestore->enabled);
        $this->assertTrue($coveragestore->exists);
        $this->assertCount(1, $coveragestore->coverages);

        return $coveragestoreName;
    }

    /**
     * @depends test_coveragestore_can_be_retrieved_by_name
     */
    public function test_coveragestores_are_retrieved($coveragestoreName)
    {
        $coveragestores = $this->geoserver->coveragestores();

        $this->assertContainsOnlyInstancesOf(CoverageStore::class, $coveragestores);

        return $coveragestoreName;
    }

    /**
     * @depends test_coveragestores_are_retrieved
     */
    public function test_coveragestore_can_be_deleted($coveragestoreName)
    {
        $coveragestore = $this->geoserver->deleteCoveragestore($coveragestoreName);

        $this->assertInstanceOf(CoverageStore::class, $coveragestore);
        $this->assertEquals(getenv('GEOSERVER_WORKSPACE'), $coveragestore->workspace);
        $this->assertTrue($coveragestore->enabled);
        $this->assertFalse($coveragestore->exists);

        return $coveragestoreName;
    }

    public function test_non_existing_coveragestore_cannot_be_retrieved()
    {
        $this->expectException(StoreNotFoundException::class);

        $coveragestore = $this->geoserver->coveragestore('some_name');
    }

    public function test_geotiff_upload_and_deleted()
    {
        $storeName = 'geotiff_test';
        $data = GeoFile::from(__DIR__ . '/../fixtures/geotiff.tiff')->name($storeName);

        $feature = $this->geoserver->upload($data);

        $this->assertInstanceOf(Coverage::class, $feature);

        $this->assertTrue($this->geoserver->exist($data), "Data not existing after upload");
        
        $deleteResult = $this->geoserver->remove($data);

        $this->assertTrue($deleteResult, "GeoFile not deleted");

        $this->assertFalse($this->geoserver->exist($data), "Data still exists after remove");
    }
}
