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

namespace OneOffTech\GeoServer\Contracts;

use Exception;

abstract class FileReader
{
    protected static function openFile($path)
    {
        if (! (is_readable($path) && is_file($path))) {
            throw new Exception("File [$path] not readable");
        }
        $handle = fopen($path, 'r');
        if (! $handle) {
            throw new Exception("Unable to read [$path] as binary file");
        }

        return $handle;
    }
    
    protected static function openFileBinary($path, $position = 0)
    {
        if (! (is_readable($path) && is_file($path))) {
            throw new Exception("File [$path] not readable");
        }
        $handle = fopen($path, 'rb');
        if (! $handle) {
            throw new Exception("Unable to read [$path] as binary file");
        }

        if ($position > 0) {
            fseek($handle, $position, SEEK_SET);
        }
        return $handle;
    }

    protected static function closeFile($handle)
    {
        if ($handle) {
            fclose($handle);
        }
    }
}
