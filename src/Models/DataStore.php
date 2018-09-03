<?php

namespace OneOffTech\GeoServer\Models;

use JMS\Serializer\Annotation as JMS;
use OneOffTech\GeoServer\Contracts\Model;

class DataStore extends Model
{
    /**
     * Name of workspace
     * @var string
     * @JMS\Type("string")
     */
    public $name;

    /**
     * The API URL to the datastore details
     * @var string
     * @JMS\Type("string")
     */
    public $href;

    /**
     *
     * @var bool
     * @JMS\Type("boolean")
     */
    public $enabled;
    
    /**
     *
     * @var bool
     * @JMS\Type("boolean")
     * @JMS\SerializedName("_default")
     */
    public $default = false;

    /**
     * @var string
     * @JMS\Type("string")
     */
    public $workspace;

    /**
     *
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("featureTypes")
     */
    public $featureTypes;

    /**
     *
     * @var string
     * @JMS\Type("array")
     * @JMS\SerializedName("connectionParameters")
     */
    public $connectionParameters;


    /**
     * Indicates if the data store exists.
     *
     * It is used to indicate the deletion status.
     * The $exists value is set to false after succesfull deletion.
     *
     * @var bool
     * @JMS\Exclude
     */
    public $exists = true;
}
