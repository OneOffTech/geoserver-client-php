<?php

namespace OneOffTech\GeoServer\Http\Responses;

use JMS\Serializer\Annotation as JMS;
use OneOffTech\GeoServer\Models\Coverage;

class CoverageStoresResponse
{
    /**
     * @var \OneOffTech\GeoServer\Models\CoverageStore[]
     * @JMS\Type("array<OneOffTech\GeoServer\Models\CoverageStore>")
     * @JMS\SerializedName("coverageStores")
     */
    public $stores;
}
