<?php
namespace Tests\Integration;

use Tests\TestCase;
use GuzzleHttp\Psr7\Request;
use OneOffTech\GeoServer\GeoFile;
use OneOffTech\GeoServer\Models\DataStore;
use OneOffTech\GeoServer\Models\Feature;
use Psr\Http\Message\RequestInterface;
use OneOffTech\GeoServer\Exception\ErrorResponseException;
use OneOffTech\GeoServer\Exception\InvalidDataException;
use Tests\Concern\SetupIntegrationTest;
use OneOffTech\GeoServer\Models\Workspace;
use OneOffTech\GeoServer\Support\ImageResponse;

class GeoServerWmsTest extends TestCase
{
    use SetupIntegrationTest;

    public function test_wms_url_is_generated_for_shapefile()
    {
        $datastoreName = 'shapefile_test';
        $file = GeoFile::from(__DIR__ . '/../fixtures/shapefile.shp')->name($datastoreName);

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
        $file = GeoFile::from(__DIR__ . '/../fixtures/geotiff.tiff')->name($datastoreName);

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
        $file = GeoFile::from(__DIR__ . '/../fixtures/shapefile.shp')->name($datastoreName);

        $resource = $this->geoserver->upload($file);

        $thumbnail = $this->geoserver->thumbnail($resource);

        $this->assertInstanceOf(ImageResponse::class, $thumbnail);
        $this->assertEquals('image/png', $thumbnail->mimeType());

        $imageAsString = $thumbnail->asString();

        list($width, $height) = getimagesizefromstring($imageAsString);

        $this->assertEquals(300, $width);
        $this->assertEquals(300, $height);

        $this->assertEquals(md5(file_get_contents(__DIR__ . '/../fixtures/shapefile_thumbnail.png')), md5($thumbnail->asString()));

        $deleteResult = $this->geoserver->remove($file);
    }
}
