<?php

namespace OneOffTech\GeoServer\Models;

use OneOffTech\GeoServer\GeoType;
use JMS\Serializer\Annotation as JMS;
use OneOffTech\GeoServer\Contracts\Model;

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
