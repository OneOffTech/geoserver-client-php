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
