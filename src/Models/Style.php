<?php

namespace OneOffTech\GeoServer\Models;

use JMS\Serializer\Annotation as JMS;
use OneOffTech\GeoServer\Contracts\Model;

/**
 * A style describes how a resource is symbolized or rendered by the Web Map Service.
 */
class Style extends Model
{
    /**
     * The style name
     *
     * @var string
     * @JMS\Type("string")
     */
    public $name;

    /**
     * The workspace in which the style is located
     *
     * @var string
     * @JMS\Type("string")
     */
    public $workspace;

    /**
     * The style format
     *
     * @var string
     * @JMS\Type("string")
     */
    public $format;

    /**
     * The style version
     *
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("languageVersion")
     */
    public $version;

    /**
     * The original style file name
     *
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("filename")
     */
    public $filename;

    /**
     * Indicates if the style exists.
     *
     * It is used to indicate the deletion status.
     * The $exists value is set to false after succesful deletion.
     *
     * @var bool
     * @JMS\Exclude
     */
    public $exists = true;
}
