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

class CoverageStore extends Store
{
    /**
     * Type of coverage store
     *
     * @var string
     * @JMS\Type("string")
     */
    public $type;

    /**
     * Location of the raster data source (often, but not necessarily, a file).
     * Can be relative to the data directory.
     *
     * @var string
     * @JMS\Type("string")
     */
    public $url;

    /**
     * The link to the coverages contained in this store
     *
     * @var array
     * @JMS\Type("array")
     */
    public $coverages;
}
