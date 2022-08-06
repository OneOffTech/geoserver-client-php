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

namespace OneOffTech\GeoServer;

/**
 * The geographical data type: vector or raster
 */
final class GeoType
{
    /**
     * Vector data
     */
    const VECTOR = "vector";

    /**
     * Raster data
     */
    const RASTER = "raster";

    /**
     * Get the GeoServer store for the specified type
     *
     * @param string $type
     * @return string
     */
    public static function storeFor($type)
    {
        return $type === 'vector' ? 'datastores' : 'coveragestores';
    }
}
