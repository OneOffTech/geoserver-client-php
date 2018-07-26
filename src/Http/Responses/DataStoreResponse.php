<?php

namespace OneOffTech\GeoServer\Http\Responses;

use JMS\Serializer\Annotation as JMS;
use OneOffTech\GeoServer\Models\DataStore;

class DataStoreResponse
{
    /**
     * @var \OneOffTech\GeoServer\Models\DataStore
     * @JMS\Type("array<OneOffTech\GeoServer\Models\DataStore>")
     * @JMS\SerializedName("dataStores")
     */
    public $dataStores;
}
