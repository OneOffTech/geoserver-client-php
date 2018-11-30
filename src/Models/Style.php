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
 * A style describes how a resource is symbolized or rendered by the Web Map Service.
 */
class Style extends Model
{
    /**
     * The style name
     *
     * @var string
     * @JMS\Type("string")
     */
    public $name;

    /**
     * The workspace in which the style is located
     *
     * @var string
     * @JMS\Type("string")
     */
    public $workspace;

    /**
     * The style format
     *
     * @var string
     * @JMS\Type("string")
     */
    public $format;

    /**
     * The style version
     *
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("languageVersion")
     */
    public $version;

    /**
     * The original style file name
     *
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("filename")
     */
    public $filename;

    /**
     * Indicates if the style exists.
     *
     * It is used to indicate the deletion status.
     * The $exists value is set to false after succesful deletion.
     *
     * @var bool
     * @JMS\Exclude
     */
    public $exists = true;
}
