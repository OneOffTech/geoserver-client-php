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
use Tests\Concern\SetupIntegrationTest;
use OneOffTech\GeoServer\Support\ImageResponse;
use Tests\Support\ImageDifference;

class GeoServerWmsTest extends TestCase
{
    use SetupIntegrationTest;

    public function test_wms_url_is_generated_for_shapefile()
    {
        $datastoreName = 'shapefile_test';
        $file = GeoFile::from(__DIR__.'/../fixtures/shapefile.shp')->name($datastoreName);

        $resource = $this->geoserver->upload($file);

        $expectedParams = "layers=".getenv('GEOSERVER_WORKSPACE').":shapefile_test&bbox=314618.446,5536155.822,315358.647,5536652.114&styles=&width=640&height=480&srs=EPSG:4326&format=image%2Fpng";
        $expectedMapUrl = sprintf("%s%s/wms?service=WMS&version=1.1.0&request=GetMap&%s", getenv('GEOSERVER_URL'), getenv('GEOSERVER_WORKSPACE'), $expectedParams);

        $mapUrlGeoFile = $this->geoserver->wmsMapUrl($file);
        $mapUrlResource = $this->geoserver->wmsMapUrl($resource);

        $this->assertEquals($expectedMapUrl, $mapUrlGeoFile);
        $this->assertEquals($mapUrlGeoFile, $mapUrlResource);

        $deleteResult = $this->geoserver->remove($file);
    }

    public function test_wms_url_is_generated_for_geotiff()
    {
        $datastoreName = 'geotiff_test';
        $file = GeoFile::from(__DIR__.'/../fixtures/geotiff.tiff')->name($datastoreName);

        $resource = $this->geoserver->upload($file);

        $expectedParams = "layers=".getenv('GEOSERVER_WORKSPACE').":geotiff_test&bbox=-83.64980947326,42.724764597616,-83.365330958964,42.964919638031&styles=&width=640&height=480&srs=EPSG:4326&format=image%2Fpng";
        $expectedMapUrl = sprintf("%s%s/wms?service=WMS&version=1.1.0&request=GetMap&%s", getenv('GEOSERVER_URL'), getenv('GEOSERVER_WORKSPACE'), $expectedParams);

        $mapUrlGeoFile = $this->geoserver->wmsMapUrl($file);
        $mapUrlResource = $this->geoserver->wmsMapUrl($resource);

        $this->assertEquals($expectedMapUrl, $mapUrlGeoFile);
        $this->assertEquals($mapUrlGeoFile, $mapUrlResource);

        $deleteResult = $this->geoserver->remove($file);
    }

    public function test_shapefile_thumbnail()
    {
        $datastoreName = 'shapefile_test';
        $file = GeoFile::from(__DIR__.'/../fixtures/shapefile.shp')->name($datastoreName);

        $resource = $this->geoserver->upload($file);

        $thumbnail = $this->geoserver->thumbnail($resource);

        $this->assertInstanceOf(ImageResponse::class, $thumbnail);
        $this->assertEquals('image/png', $thumbnail->mimeType());

        $imageAsString = $thumbnail->asString();

        list($width, $height) = getimagesizefromstring($imageAsString);

        $this->assertEquals(300, $width);
        $this->assertEquals(300, $height);

        // compare the image difference against a reference thumbnail

        file_put_contents(__DIR__.'/../fixtures/shapefile_thumbnail_from_geoserver.png', $thumbnail->asString());
        
        $differencePercentage = ImageDifference::calculate(
            __DIR__.'/../fixtures/shapefile_thumbnail.png',
            __DIR__.'/../fixtures/shapefile_thumbnail_from_geoserver.png'
        );

        unlink(__DIR__.'/../fixtures/shapefile_thumbnail_from_geoserver.png');
        $deleteResult = $this->geoserver->remove($file);

        // considering a 20% difference as acceptable
        $this->assertTrue($differencePercentage < 20);
    }
}
