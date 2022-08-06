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
use ZipArchive;
use OneOffTech\GeoServer\Contracts\FileReader;

final class ZipReader extends FileReader
{

    /**
     * Read a 32 bit integer from the beginning of a file
     *
     * @param string $path the file path to read from
     * @param bool $big_endian if the integer is in big endian notation. Default true
     * @return integer
     */
    public static function contentList($path)
    {
        $entries = [];

        $za = new ZipArchive;
        $za->open($path);

        for ($i=0; $i < $za->numFiles; $i++) {
            $entry = $za->statIndex($i);
            $entries[] = $entry['name'];
        }

        $za->close();

        return $entries;
    }

    public static function containsFile($path, $name)
    {
        $entries = [];

        $za = new ZipArchive;
        $za->open($path);

        for ($i=0; $i < $za->numFiles; $i++) {
            $entry = $za->statIndex($i);
            if (strpos($entry['name'], $name) !== false) {
                $entries[] = $entry['name'];
            }
        }

        $za->close();

        return count($entries) > 0;
    }

    /**
     * Tap into the Zip Archive
     *
     * After the callback is executed the ZIP archive is closed and saved
     *
     * @param string The zip file path
     * @param callable The function to execute when the zip file is opened. This function receive a ZipArchive instance as argument
     * @return string The zip file path
     */
    public static function tap($path, $callback)
    {
        $za = new ZipArchive;
        $za->open($path);

        try {
            $callback($za);
        } catch (Exception $ex) {
            throw $ex;
        } finally {
            $za->close();
        }

        return $path;
    }
}
