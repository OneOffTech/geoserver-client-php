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

namespace OneOffTech\GeoServer\Http;

class ResponseHelper
{
    /**
     * Check if an array is associative or index based.
     *
     * An array is considered associative if all keys are strings.
     * Empty array or null value are not considered associative
     *
     * @param array $array the array to check
     * @return true if array is associative
     */
    public static function isAssociativeArray($array)
    {
        if (empty($array) || ! is_array($array)) {
            return false;
        }

        $keys = array_keys($array);
        foreach ($keys as $key) {
            if (is_int($key)) {
                return false;
            }
        }
        return true;
    }
}
