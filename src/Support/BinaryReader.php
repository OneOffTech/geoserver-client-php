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

namespace OneOffTech\GeoServer\Support;

use Exception;
use OneOffTech\GeoServer\Contracts\FileReader;

final class BinaryReader extends FileReader
{
    private static function isBigEndianMachine()
    {
        return current(unpack('v', pack('S', 0xff))) !== 0xff;
    }

    private static function readData($path, $type, $length, $position = 0, $invert_endianness = false)
    {
        $handle = self::openFileBinary($path, $position);
        $data = fread($handle, $length);
        self::closeFile($handle);

        if ($data === false) {
            return null;
        }
        if ($invert_endianness) {
            $data = strrev($data);
        }

        return current(unpack($type, $data));
    }
    
    /**
     * Read a 32 bit integer from the beginning of a file
     *
     * @param string $path the file path to read from
     * @param bool $big_endian if the integer is in big endian notation. Default true
     * @return integer
     */
    public static function readInt32($path, $position = 0, $big_endian = true)
    {
        return self::readData($path, $big_endian ? 'N' : 'V', 4, $position);
    }
    
    public static function readShort($path, $position = 0, $big_endian = true)
    {
        return self::readData($path, 's', 2, $position);
    }
    
    public static function isGeoTiff($path)
    {
        $handle = self::openFileBinary($path);
        
        // https://www.awaresystems.be/imaging/tiff/specification/TIFF6.pdf
        // 8 bytes header:
        // - 2 bytes for the byte order
        // - 2 bytes for the TIFF header
        // - 4 bytes for the offset to the first IFD.
        $tiffHeader = fread($handle, 8);

        if ($tiffHeader === false) {
            self::closeFile($handle);
            return false;
        }

        $byteOrder = current(unpack('a', $tiffHeader)).current(unpack('a', $tiffHeader, 1));

        if (! in_array($byteOrder, ['MM', 'II'])) {
            // unknown byte order
            self::closeFile($handle);
            return false;
        }

        $big_endian = $byteOrder === 'MM' ? true : false;
        $tiffCode = current(unpack('s', $tiffHeader, 2));

        if ($tiffCode !== 42) {
            // tiff code not found
            self::closeFile($handle);
            return false;
        }

        $byteOffset = self::getBytes($tiffHeader, 4, 4, $big_endian);

        fseek($handle, $byteOffset);

        $numDirData = fread($handle, 2);

        $numDirEntries = self::getBytes($numDirData, 2, 0, $big_endian);
        fseek($handle, $byteOffset+2);

        $imageFileDirectoriesData = fread($handle, (12 * $numDirEntries)+12);

        // from the Image File Directories record in the TIFF file I need the GeoKeyDirectory
        // and the values in the GeoKeyDirectory, which has field code 34735
        // https://www.geospatialworld.net/article/geotiff-a-standard-image-file-format-for-gis-applications/

        // Even if from https://github.com/xlhomme/GeotiffParser.js/blob/master/js/GeotiffParser.js
        // seems that the GeoKeyDirectory field should have 4 values to be a valid GeoTiff
        // we consider the presence of the tag a valid indicator
        
        $hasGeoKeyDirectory = false;
        for ($i = 0, $entryCount = 0; $entryCount < $numDirEntries; $i += 12, $entryCount++) {
            $fieldTag = self::getBytes($imageFileDirectoriesData, 2, $i, $big_endian);

            if ($fieldTag === 34735) { // GeoKeyDirectory field
                $hasGeoKeyDirectory = true;
                break;
            }
        }

        self::closeFile($handle);

        return $hasGeoKeyDirectory;
    }

    public static function isGeoPackage($path)
    {
        $handle = self::openFileBinary($path);
        $sqliteMagic = self::getString(fread($handle, 16), 15);
        fseek($handle, 68);
        $gpkgMagic = self::getString(fread($handle, 4), 4);
        self::closeFile($handle);

        if ($sqliteMagic !== 'SQLite format 3') {
            return false;
        }

        if ($gpkgMagic !== 'GPKG') {
            return false;
        }

        return true;
    }

    private static function getBytes($data, $length, $offset = 0, $big_endian = true)
    {
        if ($length <= 2) {
            return current(unpack($big_endian ? 'n' : 'v', $data, $offset));
        } elseif ($length <= 4) {
            return current(unpack($big_endian ? 'N' : 'V', $data, $offset));
        }
        // unsigned short 16bit current(unpack($big_endian ? 'n' : 'v', $tiffHeader, 4));
        // unsigned long 32bit current(unpack($big_endian ? 'N' : 'V', $tiffHeader, 4));
    }

    private static function getString($data, $length, $offset = 0)
    {
        try {
            $chars = [];

            for ($i=$offset; $i < $length; $i++) {
                $chars[] = current(unpack('a', $data, $i));
            }

            return implode('', $chars);
        } catch (Exception $ex) {
            return '';
        }
    }
}
