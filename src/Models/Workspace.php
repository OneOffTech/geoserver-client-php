<?php

namespace OneOffTech\GeoServer\Models;

use JMS\Serializer\Annotation as JMS;
use OneOffTech\GeoServer\Contracts\Model;


class Workspace extends Model
{

    /**
     * Name of workspace
     * @var string
     * @JMS\Type("string")
     */
    public $name;

    /**
     * The API URL to the workspace details
     * @var string
     * @JMS\Type("string")
     */
    public $href;

    /**
     * If the workspace is isolated
     * @var bool
     * @JMS\Type("boolean")
     */
    public $isolated;

    /**
     * URL to Datas tores in this workspace
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("dataStores")
     */
    public $dataStores;

    /**
     * URL to Coverage stores in this workspace
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("coverageStores")
     */
    public $coverageStores;

    /**
     * URL to WMS stores in this workspace
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("wmsStores")
     */
    public $wmsStores;

    /**
     * URL to WMS stores in this workspace
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("wmtsStores")
     */
    public $wmtsStores;
}
