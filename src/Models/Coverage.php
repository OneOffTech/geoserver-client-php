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

use OneOffTech\GeoServer\GeoType;
use JMS\Serializer\Annotation as JMS;

/**
 * A coverage is a raster data set which originates from a coverage store.
 */
final class Coverage extends Resource
{
  
    /**
     *
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("nativeFormat")
     */
    public $nativeFormat;

    /**
     * @var array
     * @JMS\Type("array")
     * @JMS\SerializedName("interpolationMethods")
     */
    public $interpolationMethods;

    /**
     * @var array
     * @JMS\Type("array")
     */
    public $nativeCRS;

    /**
     *
     * @var array
     * @JMS\Type("array")
     */
    public $dimensions;

    /**
     * Contains information about how to translate from the raster plan to a coordinate reference system
     * @var array
     * @JMS\Type("array")
     */
    public $grid;

    public function type()
    {
        return GeoType::RASTER;
    }
}
