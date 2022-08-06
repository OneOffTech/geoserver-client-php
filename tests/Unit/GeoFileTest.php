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
use OneOffTech\GeoServer\GeoFile;
use OneOffTech\GeoServer\GeoType;
use OneOffTech\GeoServer\GeoFormat;

class GeoFileTest extends TestCase
{
    public function supported_files()
    {
        return [
            [__DIR__.'/../fixtures/shapefile.shp'],
            [__DIR__.'/../fixtures/shapefile.zip'],
            [__DIR__.'/../fixtures/geotiff.tiff'],
            [__DIR__.'/../fixtures/empty.gpkg'],
        ];
    }
    
    public function unsupported_files()
    {
        return [
            [__DIR__.'/../fixtures/plain.json'],
            [__DIR__.'/../fixtures/plain.zip'],
            [__DIR__.'/../fixtures/tiff.tif'],
            [__DIR__.'/../fixtures/geojson.geojson'],
            [__DIR__.'/../fixtures/geojson-in-plain-json.json'],
            [__DIR__.'/../fixtures/kml.kml'],
            [__DIR__.'/../fixtures/kmz.kmz'],
            [__DIR__.'/../fixtures/gpx.gpx'],
        ];
    }

    /**
     * @dataProvider supported_files
     */
    public function test_supported_function_identifies_supported_files($file)
    {
        $this->assertTrue(GeoFile::isSupported($file));
    }

    /**
     * @dataProvider unsupported_files
     */
    public function test_supported_function_reject_unsupported_files($file)
    {
        $this->assertFalse(GeoFile::isSupported($file));
    }

    public function test_shapefile_is_recognized()
    {
        $file = GeoFile::from(__DIR__.'/../fixtures/shapefile.shp');

        $this->assertInstanceOf(GeoFile::class, $file);
        $this->assertEquals(GeoFormat::SHAPEFILE, $file->format);
        $this->assertEquals(GeoType::VECTOR, $file->type);
        $this->assertEquals('application/octet-stream', $file->mimeType);
        $this->assertEquals('shp', $file->extension);
        $this->assertEquals('shapefile.shp', $file->name);
        $this->assertEquals($file->originalName, $file->name);
    }
    
    public function test_shapefile_packed_in_zip_is_recognized()
    {
        $file = GeoFile::from(__DIR__.'/../fixtures/shapefile.zip');

        $this->assertInstanceOf(GeoFile::class, $file);
        $this->assertEquals(GeoFormat::SHAPEFILE_ZIP, $file->format);
        $this->assertEquals(GeoType::VECTOR, $file->type);
        $this->assertEquals('application/zip', $file->mimeType);
        $this->assertEquals('zip', $file->extension);
        $this->assertEquals('shapefile.zip', $file->name);
        $this->assertEquals($file->originalName, $file->name);
    }

    public function test_geotiff_is_recognized()
    {
        $file = GeoFile::from(__DIR__.'/../fixtures/geotiff.tiff');

        $this->assertInstanceOf(GeoFile::class, $file);
        $this->assertEquals(GeoFormat::GEOTIFF, $file->format);
        $this->assertEquals(GeoType::RASTER, $file->type);
        $this->assertEquals('image/tiff', $file->mimeType);
        $this->assertEquals('tiff', $file->extension);
        $this->assertEquals('geotiff.tiff', $file->name);
        $this->assertEquals($file->originalName, $file->name);
    }

    public function test_geopackage_is_recognized()
    {
        $file = GeoFile::from(__DIR__.'/../fixtures/empty.gpkg');

        $this->assertInstanceOf(GeoFile::class, $file);
        $this->assertEquals(GeoFormat::GEOPACKAGE, $file->format);
        $this->assertEquals(GeoType::VECTOR, $file->type);
        $this->assertEquals('application/geopackage+sqlite3', $file->mimeType);
        $this->assertEquals('gpkg', $file->extension);
        $this->assertEquals('empty.gpkg', $file->name);
        $this->assertEquals($file->originalName, $file->name);
    }

    public function test_copy_to_temporary()
    {
        $file = GeoFile::from(__DIR__.'/../fixtures/buildings.zip');

        $this->assertInstanceOf(GeoFile::class, $file);
        $this->assertEquals(GeoFormat::SHAPEFILE_ZIP, $file->format);
        $this->assertEquals(GeoType::VECTOR, $file->type);
        $this->assertEquals('application/zip', $file->mimeType);
        $this->assertEquals('zip', $file->extension);
        $this->assertEquals('buildings.zip', $file->name);
        $this->assertEquals($file->originalName, $file->name);

        $copy = $file->copy();

        $this->assertInstanceOf(GeoFile::class, $copy);
        $this->assertEquals(GeoFormat::SHAPEFILE_ZIP, $copy->format);
        $this->assertEquals(GeoType::VECTOR, $copy->type);
        $this->assertEquals('application/zip', $copy->mimeType);
        $this->assertNotEquals($copy->originalName, $copy->name);
        $this->assertEquals($file->name, $copy->name);
        $this->assertEquals($file->content(), $copy->content());

        unlink($copy->path());
    }
}
