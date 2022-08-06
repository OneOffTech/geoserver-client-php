<?php
/*
 *    GeoServer PHP Client
 *
 *    Copyright (c) 2018 Oneoff-tech UG, Germany, www.oneofftech.xyz
 *
 *    This program is Free Software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public
 *    License along with this program.  If not, see
 *    <http://www.gnu.org/licenses/>.
 */

namespace Tests\Integration;

use Tests\TestCase;
use OneOffTech\GeoServer\GeoFile;
use OneOffTech\GeoServer\GeoType;
use Tests\Concern\SetupIntegrationTest;
use OneOffTech\GeoServer\Models\Coverage;
use OneOffTech\GeoServer\Models\CoverageStore;
use OneOffTech\GeoServer\Exception\StoreNotFoundException;

class GeoServerCoverageStoresTest extends TestCase
{
    use SetupIntegrationTest;

    public function test_geotiff_can_be_uploaded()
    {
        $storeName = 'geotiff_test';
        $data = GeoFile::from(__DIR__.'/../fixtures/geotiff.tiff')->name($storeName);

        $coverage = $this->geoserver->upload($data);

        $this->assertInstanceOf(Coverage::class, $coverage);
        $this->assertEquals(GeoType::RASTER, $coverage->type());
        $this->assertEquals("geotiff_test", $coverage->name);
        $this->assertEquals("geotiff_test", $coverage->title);
        $this->assertEquals("geotiff_test", $coverage->nativeName);
        $this->assertEquals("GeoTIFF", $coverage->nativeFormat);
        $this->assertFalse($coverage->skipNumberMatched);
        $this->assertFalse($coverage->circularArcPresent);
        $this->assertNotNull($coverage->store);
        $this->assertNotNull($coverage->keywords);
        $this->assertNotNull($coverage->nativeBoundingBox);
        $this->assertNotNull($coverage->boundingBox);
        $this->assertNotEmpty($coverage->interpolationMethods);
        $this->assertEquals(78999, $coverage->nativeBoundingBox->minX);
        $this->assertEquals(1412948.0000000002, $coverage->nativeBoundingBox->minY);
        $this->assertEquals(101839, $coverage->nativeBoundingBox->maxX);
        $this->assertEquals(1439268.0000000002, $coverage->nativeBoundingBox->maxY);
        $this->assertEquals(-83.64980947326015, $coverage->boundingBox->minX);
        $this->assertEquals(42.724764597615966, $coverage->boundingBox->minY);
        $this->assertEquals(-83.36533095896407, $coverage->boundingBox->maxX);
        $this->assertEquals(42.96491963803106, $coverage->boundingBox->maxY);
        $this->assertEquals("EPSG:4326", $coverage->boundingBox->crs);

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
        $data = GeoFile::from(__DIR__.'/../fixtures/geotiff.tiff')->name($storeName);

        $feature = $this->geoserver->upload($data);

        $this->assertInstanceOf(Coverage::class, $feature);

        $this->assertTrue($this->geoserver->exist($data), "Data not existing after upload");
        
        $deleteResult = $this->geoserver->remove($data);

        $this->assertTrue($deleteResult, "GeoFile not deleted");

        $this->assertFalse($this->geoserver->exist($data), "Data still exists after remove");
    }
}
