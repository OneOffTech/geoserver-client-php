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

namespace OneOffTech\GeoServer\Exception;

/**
 * Raised when a style cannot be found on the specific geoserver instance
 */
class StyleNotFoundException extends GeoServerClientException
{
    /**
     *
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message, 404);
    }

    /**
     * Create a style not found exception for a given style
     *
     * @param string $name the style name
     * @param string $workspace the workspace that was expecting to contain the style
     * @return StyleNotFoundException
     */
    public static function style($name, $workspace)
    {
        return new self("The style [$name] cannot be found in [$workspace].");
    }
}
