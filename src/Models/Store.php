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

namespace OneOffTech\GeoServer\Models;

use JMS\Serializer\Annotation as JMS;
use OneOffTech\GeoServer\Contracts\Model;

/**
 * A generic store, can be a data store or a coverage store
 */
class Store extends Model
{
    /**
     * Store name
     *
     * @var string
     * @JMS\Type("string")
     */
    public $name;

    /**
     * The API URL to the store details
     *
     * @var string
     * @JMS\Type("string")
     */
    public $href;

    /**
     * If the store is enabled
     *
     * @var bool
     * @JMS\Type("boolean")
     */
    public $enabled;
    
    /**
     * If the store is the default one
     *
     * @var bool
     * @JMS\Type("boolean")
     * @JMS\SerializedName("_default")
     */
    public $default = false;

    /**
     * The workspace in which the store is located
     *
     * @var string
     * @JMS\Type("string")
     */
    public $workspace;

    /**
     * Indicates if the store exists.
     *
     * It is used to indicate the deletion status.
     * The $exists value is set to false after succesful deletion.
     *
     * @var bool
     * @JMS\Exclude
     */
    public $exists = true;
}
