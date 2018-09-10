<?php

namespace OneOffTech\GeoServer\Models;

use OneOffTech\GeoServer\GeoType;
use JMS\Serializer\Annotation as JMS;
use OneOffTech\GeoServer\Contracts\Model;

/**
 * A feature type is a vector based spatial resource or data set that originates from a data store
 */
final class Feature extends Resource
{
    /**
     * The identifier of coordinate reference system of the resource.
     * @var string
     * @JMS\Type("string")
     */
    public $srs;

    /**
     *
     * @var bool
     * @JMS\Type("boolean")
     * @JMS\SerializedName("overridingServiceSRS")
     */
    public $overridingServiceSRS = false;

    public function type()
    {
        return GeoType::VECTOR;
    }
}
