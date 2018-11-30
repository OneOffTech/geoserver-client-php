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

class Workspace extends Model
{

    /**
     * Name of workspace
     * @var string
     * @JMS\Type("string")
     */
    public $name;

    /**
     * The API URL to the workspace details
     * @var string
     * @JMS\Type("string")
     */
    public $href;

    /**
     * If the workspace is isolated
     * @var bool
     * @JMS\Type("boolean")
     */
    public $isolated;

    /**
     * URL to Datas tores in this workspace
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("dataStores")
     */
    public $dataStores;

    /**
     * URL to Coverage stores in this workspace
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("coverageStores")
     */
    public $coverageStores;

    /**
     * URL to WMS stores in this workspace
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("wmsStores")
     */
    public $wmsStores;

    /**
     * URL to WMS stores in this workspace
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("wmtsStores")
     */
    public $wmtsStores;
}
