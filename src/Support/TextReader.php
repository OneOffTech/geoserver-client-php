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

use OneOffTech\GeoServer\Contracts\FileReader;

final class TextReader extends FileReader
{
    
    /**
     * Read a line from file
     *
     * @param string $path the file path to read from
     * @param integer $lines the number of lines to read
     * @return array
     */
    public static function readLines($path, $lines = 1)
    {
        $data = [];

        $handle = self::openFile($path);
        for ($i=0; $i < $lines; $i++) {
            $data[] = fgets($handle);
        }
        self::closeFile($handle);

        return $data;
    }
}
