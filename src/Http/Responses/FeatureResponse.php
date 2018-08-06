<?php

namespace OneOffTech\GeoServer\Http\Responses;

use JMS\Serializer\Annotation as JMS;
use OneOffTech\GeoServer\Models\Feature;

class FeatureResponse
{
    /**
     * @var \OneOffTech\GeoServer\Models\Feature
     * @JMS\Type("OneOffTech\GeoServer\Models\Feature")
     * @JMS\SerializedName("featureType")
     */
    public $feature;
}
