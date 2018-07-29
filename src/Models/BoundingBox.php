<?php

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
     *
     * @var string
     * @JMS\Type("string")
     */
    public $crs = null;
}
