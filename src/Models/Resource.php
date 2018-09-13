<?php

namespace OneOffTech\GeoServer\Models;

use JMS\Serializer\Annotation as JMS;
use OneOffTech\GeoServer\Contracts\Model;

/**
 * A resource that describe both a Coverage or a Feature.
 */
abstract class Resource extends Model
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
     * This name corresponds to the physical resource that is
     * derived from -- a shapefile name, a database table,...
     *
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("nativeName")
     */
    public $nativeName;
  
    /**
     * The title of the resource.
     * This is usually something that is meant to be displayed in a user interface.
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
     * The store the resource is a part of.
     * @var array
     * @JMS\Type("array")
     */
    public $store;

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
     * Wrapper for the derived set of attributes for the feature type
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

    /**
     * Return the type of the resource
     *
     * @return string The @see GeoType of the resource
     */
    abstract public function type();
}
