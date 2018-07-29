<?php

namespace OneOffTech\GeoServer;

/**
 * The geographical data type: vector or raster
 */
final class GeoType
{
    /**
     * Vector data
     */
    const VECTOR = "vector";

    /**
     * Raster data
     */
    const RASTER = "raster";


    /**
     * Get the GeoServer store for the specified type
     *
     * @param string $type
     * @return string
     */
    public static function storeFor($type)
    {
        return $type === 'vector' ? 'datastore' : 'coveragestore';
    }
}
