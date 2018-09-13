<?php

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
