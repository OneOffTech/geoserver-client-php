<?php

namespace OneOffTech\GeoServer\Http\Responses;

use JMS\Serializer\Annotation as JMS;
use OneOffTech\GeoServer\Models\Workspace;

class WorkspaceResponse
{
    /**
     * @var \OneOffTech\GeoServer\Models\Workspace
     * @JMS\Type("OneOffTech\GeoServer\Models\Workspace")
     */
    public $workspace;
}
