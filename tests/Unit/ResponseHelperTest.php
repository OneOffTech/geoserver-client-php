<?php

namespace Tests\Unit;

use Tests\TestCase;
use OneOffTech\GeoServer\Http\ResponseHelper;

class ResponseHelperTest extends TestCase
{
    public function testAssociativeArrayIsRecognized()
    {
        $array = [
            'hello' => 'value',
            'key' => 'value',
        ];

        $this->assertTrue(ResponseHelper::isAssociativeArray($array));
    }

    public function testMixedArrayIsNotRecognizedAsAssociative()
    {
        $array = [
            'zero' => 'value',
            0 => 'value',
            'key' => 'value',
        ];

        $this->assertFalse(ResponseHelper::isAssociativeArray($array));
    }

    public function testIndexArrayIsNotRecognizedAsAssociative()
    {
        $array = [
            'value1',
            'value2',
        ];

        $this->assertFalse(ResponseHelper::isAssociativeArray($array));
    }
    
    public function testNullAndEmptyAreNotRecognizedAsAssociative()
    {
        $this->assertFalse(ResponseHelper::isAssociativeArray(null));
        $this->assertFalse(ResponseHelper::isAssociativeArray([]));
    }
}
