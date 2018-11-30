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

class BoundingBox
{
    /**
     *
     * @var float
     * @JMS\Type("float")
     * @JMS\SerializedName("minx")
     */
    public $minX;

    /**
     *
     * @var float
     * @JMS\Type("float")
     * @JMS\SerializedName("miny")
     */
    public $minY;

    /**
     *
     * @var float
     * @JMS\Type("float")
     * @JMS\SerializedName("maxx")
     */
    public $maxX;

    /**
     *
     * @var float
     * @JMS\Type("float")
     * @JMS\SerializedName("maxy")
     */
    public $maxY;

    /**
     * The coordinate system in which the bounding box values are expressed
     *
     * @var string
     * @JMS\Type("string")
     */
    public $crs = null;

    public function toArray()
    {
        return [$this->minX, $this->minY, $this->maxX, $this->maxY];
    }
}
