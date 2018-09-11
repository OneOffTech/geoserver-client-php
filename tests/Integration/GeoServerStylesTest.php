<?php
namespace Tests\Integration;

use Tests\TestCase;
use GuzzleHttp\Psr7\Request;
use OneOffTech\GeoServer\StyleFile;
use Psr\Http\Message\RequestInterface;
use OneOffTech\GeoServer\Models\Style;
use Tests\Concern\SetupIntegrationTest;
use OneOffTech\GeoServer\Exception\InvalidDataException;
use OneOffTech\GeoServer\Exception\ErrorResponseException;
use OneOffTech\GeoServer\Exception\StyleNotFoundException;

class GeoServerStylesTest extends TestCase
{
    use SetupIntegrationTest;

    public function test_styles_can_be_uploaded()
    {
        $styleName = 'style_test';
        $data = StyleFile::from(__DIR__ . '/../fixtures/style.sld')->name($styleName);

        $feature = $this->geoserver->uploadStyle($data);

        $this->assertInstanceOf(Style::class, $feature);


        return $styleName;
    }

    /**
     * @depends test_styles_can_be_uploaded
     */
    public function test_style_can_be_retrieved_by_name($styleName)
    {
        $style = $this->geoserver->style($styleName);

        $this->assertInstanceOf(Style::class, $style);

        return $styleName;
    }

    /**
     * @depends test_style_can_be_retrieved_by_name
     */
    public function test_styles_are_retrieved($datastoreName)
    {
        $styles = $this->geoserver->styles();

        $this->assertContainsOnlyInstancesOf(DataStore::class, $styles);

        return $datastoreName;
    }

    /**
     * @depends test_styles_are_retrieved
     */
    public function test_style_can_be_deleted($styleName)
    {
        $style = $this->geoserver->removeStyle($styleName);

        $this->assertInstanceOf(Style::class, $style);
        $this->assertTrue($style->enabled);
        $this->assertFalse($style->exists);
    }

    public function test_non_existing_style_cannot_be_retrieved()
    {
        $this->expectException(StyleNotFoundException::class);

        $style = $this->geoserver->style('some_name');
    }

}
