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
 * Raised when a style cannot be uploaded as a previous style with the same name already exists
 */
class StyleAlreadyExistsException extends GeoServerClientException
{
    /**
     *
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message, 409);
    }

    /**
     * Create a style already exists exception for a given style
     *
     * @param string $name the style name
     * @param string $workspace the workspace that contains the style
     * @return StyleAlreadyExistsException
     */
    public static function style($name, $workspace)
    {
        return new self("A style named [$name] already exists in [$workspace].");
    }
}
