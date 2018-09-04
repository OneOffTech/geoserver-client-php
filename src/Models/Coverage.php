<?php

namespace OneOffTech\GeoServer\Models;

use JMS\Serializer\Annotation as JMS;
use OneOffTech\GeoServer\Contracts\Model;

/**
 * A coverage is a raster data set which originates from a coverage store.
 */
class Coverage extends Model
{
    /**
     * The name of the resource.
     * 
     * @var string
     * @JMS\Type("string")
     */
    public $name;
    
    /**
     * The native name of the resource.
     * 
     * This name corresponds to the physical resource that feature type is 
     * derived from -- a shapefile name, a database table, etc...
     * 
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("nativeName")
     */
    public $nativeName;
  
    /**
     * 
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("nativeFormat")
     */
    public $nativeFormat;

    /**
     * @var array
     * @JMS\Type("array")
     * @JMS\SerializedName("interpolationMethods")
     */
    public $interpolationMethods;

    /**
     * The title of the resource. This is usually something that is meant to be displayed in a user interface.
     * 
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
     * @var array
     * @JMS\Type("array")
     */
    public $nativeCRS;

    /**
     *
     * @var bool
     * @JMS\Type("boolean")
     * @JMS\SerializedName("enabled")
     */
    public $enabled = true;
    
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
     * The bounds of the resource in lat / lon. This value represents a "fixed value" and is not calulated on the underlying dataset
     *
     * @var \OneOffTech\GeoServer\Models\BoundingBox
     * @JMS\Type("OneOffTech\GeoServer\Models\BoundingBox")
     * @JMS\SerializedName("latLonBoundingBox")
     */
    public $latLonBoundingBox;

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
     * @var array
     * @JMS\Type("array")
     */
    public $dimensions;

    /**
     * Contains information about how to translate from the raster plan to a coordinate reference system
     * @var array
     * @JMS\Type("array")
     */
    public $grid;

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
