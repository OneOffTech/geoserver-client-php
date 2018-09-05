<?php

namespace OneOffTech\GeoServer\Http\Responses;

use JMS\Serializer\Annotation as JMS;
use OneOffTech\GeoServer\Models\Coverage;

class CoverageResponse
{
    /**
     * @var \OneOffTech\GeoServer\Models\Coverage
     * @JMS\Type("OneOffTech\GeoServer\Models\Coverage")
     * @JMS\SerializedName("coverage")
     */
    public $coverage;
}
