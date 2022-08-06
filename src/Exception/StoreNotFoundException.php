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
 * Raised when a store cannot be found on the specific geoserver instance
 */
class StoreNotFoundException extends GeoServerClientException
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
     * Create a store not found exception for a data store
     *
     * @param string $name the data store name
     * @return StoreNotFoundException
     */
    public static function datastore($name)
    {
        return new self("Data store [$name] not found.");
    }
    
    /**
     * Create a store not found exception for a coverage store
     *
     * @param string $name the coverage store name
     * @return StoreNotFoundException
     */
    public static function coveragestore($name)
    {
        return new self("Coverage store [$name] not found.");
    }
}
