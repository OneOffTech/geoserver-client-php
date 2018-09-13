<?php
namespace Tests\Unit;

use LogicException;
use Tests\TestCase;
use InvalidArgumentException;
use OneOffTech\GeoServer\Models\BoundingBox;
use OneOffTech\GeoServer\Support\WmsOptions;

class WmsOptionsTest extends TestCase
{
    public function test_default_values_are_used()
    {
        $bbox = new BoundingBox();
        $bbox->minX = -83.64980947326015;
        $bbox->minY = 42.724764597615966;
        $bbox->maxX = -83.36533095896407;
        $bbox->maxY = 42.96491963803106;

        $options = (new WmsOptions())->layers('workspace:layer')->boundingBox($bbox);

        $this->assertInstanceOf(WmsOptions::class, $options);
        
        $this->assertEquals([
            'request' => "GetMap",
            'version' => "1.1.0",
            'format' => "image/png",
            'layers' => ["workspace:layer"],
            'bbox' => [-83.64980947326015, 42.724764597615966, -83.36533095896407, 42.96491963803106],
            'srs' => "EPSG:4326",
            'width' => 640,
            'height' => 480,
            'styles' => [],
        ], $options->toArray());
    }
    
    public function test_not_setting_layer_generate_exception_when_serializing()
    {
        $options = (new WmsOptions());

        $this->expectException(LogicException::class);

        $options->toArray();
    }
    
    public function test_not_setting_bounding_box_generate_exception_when_serializing()
    {
        $options = (new WmsOptions())->layers('workspace:layer');

        $this->expectException(LogicException::class);

        $options->toArray();
    }
    
    public function test_setting_wrong_format_raises_exception()
    {
        $this->expectException(InvalidArgumentException::class);

        $options = (new WmsOptions())->format('workspace:layer');
    }
}
