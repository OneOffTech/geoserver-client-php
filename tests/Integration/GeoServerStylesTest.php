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
use OneOffTech\GeoServer\StyleFile;
use OneOffTech\GeoServer\Models\Style;
use Tests\Concern\SetupIntegrationTest;
use OneOffTech\GeoServer\Exception\StyleNotFoundException;

class GeoServerStylesTest extends TestCase
{
    use SetupIntegrationTest;

    public function test_styles_can_be_uploaded()
    {
        $styleName = 'style_test';
        $data = StyleFile::from(__DIR__.'/../fixtures/style.sld')->name($styleName);

        $style = $this->geoserver->uploadStyle($data);

        $this->assertInstanceOf(Style::class, $style);
        $this->assertEquals($styleName, $style->name);
        $this->assertEquals(getenv('GEOSERVER_WORKSPACE'), $style->workspace);
        $this->assertEquals('style_test.sld', $style->filename);
        $this->assertEquals('sld', $style->format);
        $this->assertEquals('1.0.0', $style->version);
        $this->assertTrue($style->exists, "Style not existing");

        return $styleName;
    }

    /**
     * @depends test_styles_can_be_uploaded
     */
    public function test_style_can_be_retrieved_by_name($styleName = 'style_test')
    {
        $style = $this->geoserver->style($styleName);

        $this->assertInstanceOf(Style::class, $style);
        $this->assertEquals($styleName, $style->name);
        $this->assertEquals(getenv('GEOSERVER_WORKSPACE'), $style->workspace);
        $this->assertEquals('style_test.sld', $style->filename);
        $this->assertEquals('sld', $style->format);
        $this->assertEquals('1.0.0', $style->version);
        $this->assertTrue($style->exists, "Style not existing");

        return $styleName;
    }

    /**
     * @depends test_style_can_be_retrieved_by_name
     */
    public function test_styles_are_retrieved($datastoreName)
    {
        $styles = $this->geoserver->styles();

        $this->assertContainsOnlyInstancesOf(Style::class, $styles);

        return $datastoreName;
    }

    /**
     * @depends test_styles_are_retrieved
     */
    public function test_style_can_be_deleted($styleName)
    {
        $style = $this->geoserver->removeStyle($styleName);

        $this->assertInstanceOf(Style::class, $style);
        $this->assertEquals($styleName, $style->name);
        $this->assertEquals(getenv('GEOSERVER_WORKSPACE'), $style->workspace);
        $this->assertEquals('style_test.sld', $style->filename);
        $this->assertEquals('sld', $style->format);
        $this->assertEquals('1.0.0', $style->version);
        $this->assertFalse($style->exists, "Style still exists after deletion");
    }

    public function test_non_existing_style_cannot_be_retrieved()
    {
        $this->expectException(StyleNotFoundException::class);

        $style = $this->geoserver->style('some_name');
    }
}
