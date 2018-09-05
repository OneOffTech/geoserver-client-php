<?php

namespace OneOffTech\GeoServer\Http\Responses;

use JMS\Serializer\Annotation as JMS;
use OneOffTech\GeoServer\Models\Coverage;

class CoverageStoreResponse
{
    /**
     * @var \OneOffTech\GeoServer\Models\CoverageStore
     * @JMS\Type("OneOffTech\GeoServer\Models\CoverageStore")
     * @JMS\SerializedName("coverageStore")
     */
    public $store;
}
