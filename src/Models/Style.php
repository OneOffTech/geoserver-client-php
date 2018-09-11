<?php

namespace OneOffTech\GeoServer\Models;

use JMS\Serializer\Annotation as JMS;
use OneOffTech\GeoServer\Contracts\Model;

/**
 * A style describes how a resource is symbolized or rendered by the Web Map Service.
 */
class Style extends Model
{
    /**
     * Store name
     * 
     * @var string
     * @JMS\Type("string")
     */
    public $name;

    /**
     * The API URL to the style details
     * 
     * @var string
     * @JMS\Type("string")
     */
    public $href;

    /**
     * If the style is enabled
     * 
     * @var bool
     * @JMS\Type("boolean")
     */
    public $enabled;

    /**
     * The workspace in which the style is located
     * 
     * @var string
     * @JMS\Type("string")
     */
    public $workspace;

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
