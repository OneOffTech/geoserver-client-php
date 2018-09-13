<?php

namespace OneOffTech\GeoServer\Http\Responses;

use JMS\Serializer\Annotation as JMS;
use OneOffTech\GeoServer\Models\Style;

class StylesResponse
{
    /**
     * @var \OneOffTech\GeoServer\Models\Style[]
     * @JMS\Type("array<OneOffTech\GeoServer\Models\Style>")
     * @JMS\SerializedName("styles")
     */
    public $styles;
}
