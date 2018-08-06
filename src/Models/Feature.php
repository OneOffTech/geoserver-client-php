<?php

namespace OneOffTech\GeoServer\Models;

use JMS\Serializer\Annotation as JMS;
use OneOffTech\GeoServer\Contracts\Model;

class Feature extends Model
{
    /**
     * The name of the resource.
     * @var string
     * @JMS\Type("string")
     */
    public $name;

    /**
     * The title of the resource. This is usually something that is meant to be displayed in a user interface.
     * @var string
     * @JMS\Type("string")
     */
    public $title;

    /**
     * A description of the resource. This is usually something that is meant to be displayed in a user interface.
     * @var string
     * @JMS\Type("string")
     */
    public $abstract;
    
    /**
     *
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("nativeName")
     */
    public $nativeName;

    /**
     * The identifier of coordinate reference system of the resource.
     * @var string
     * @JMS\Type("string")
     */
    public $srs;

    /**
     *
     * @var bool
     * @JMS\Type("boolean")
     */
    public $enabled = true;
    /**
     *
     * @var bool
     * @JMS\Type("boolean")
     * @JMS\SerializedName("overridingServiceSRS")
     */
    public $overridingServiceSRS = false;
    /**
     * True if this feature type info is overriding the counting of numberMatched
     * @var bool
     * @JMS\Type("boolean")
     * @JMS\SerializedName("skipNumberMatched")
     */
    public $skipNumberMatched = false;
    /**
     *
     * @var bool
     * @JMS\Type("boolean")
     * @JMS\SerializedName("circularArcPresent")
     */
    public $circularArcPresent = false;

    /**
     * The store the resource is a part of.
     * @var array
     * @JMS\Type("array")
     */
    public $store;

    /**
     * A collection of keywords associated with the resource.
     * @var array
     * @JMS\Type("array")
     */
    public $keywords;


    /**
     * Returns the bounds of the resource in its declared CRS.
     *
     * @var \OneOffTech\GeoServer\Models\BoundingBox
     * @JMS\Type("OneOffTech\GeoServer\Models\BoundingBox")
     * @JMS\SerializedName("nativeBoundingBox")
     */
    public $nativeBoundingBox;

    /**
     * The bounds of the resource in lat / lon. This value represents a "fixed value" and is not calculated on the underlying dataset.
     *
     * @var \OneOffTech\GeoServer\Models\BoundingBox
     * @JMS\Type("OneOffTech\GeoServer\Models\BoundingBox")
     * @JMS\SerializedName("latLonBoundingBox")
     */
    public $boundingBox;

    /**
     *
     * @var array
     * @JMS\Type("array")
     */
    public $attributes;

    /**
     *
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("projectionPolicy")
     */
    public $projectionPolicy;

    /**
     *
     * @var float
     * @JMS\Type("float")
     * @JMS\SerializedName("maxFeatures")
     */
    public $maxFeatures;

    /**
     *
     * @var float
     * @JMS\Type("float")
     * @JMS\SerializedName("numDecimals")
     */
    public $numDecimals;
    
    /**
     * @var array
     * @JMS\Type("array")
     */
    public $namespace;
}
