<?php

namespace OneOffTech\GeoServer\Models;

use JMS\Serializer\Annotation as JMS;

class CoverageStore extends Store
{
    /**
     * Type of coverage store
     * 
     * @var string
     * @JMS\Type("string")
     */
    public $type;

    /**
     * Location of the raster data source (often, but not necessarily, a file). 
     * Can be relative to the data directory.
     * 
     * @var string
     * @JMS\Type("string")
     */
    public $url;

    /**
     * The link to the coverages contained in this store
     * 
     * @var array
     * @JMS\Type("array")
     */
    public $coverages;
}
