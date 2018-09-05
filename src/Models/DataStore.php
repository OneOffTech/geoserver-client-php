<?php

namespace OneOffTech\GeoServer\Models;

use JMS\Serializer\Annotation as JMS;

class DataStore extends Store
{
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
}
