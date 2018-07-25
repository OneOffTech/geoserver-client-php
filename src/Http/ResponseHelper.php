<?php
namespace OneOffTech\GeoServer\Http;

class ResponseHelper
{
    /**
     * Check if an array is associative or index based.
     *
     * An array is considered associative if all keys are strings.
     * Empty array or null value are not considered associative
     *
     * @param array $array the array to check
     * @return true if array is associative
     */
    public static function isAssociativeArray($array)
    {
        if (empty($array) || !is_array($array)) {
            return false;
        }

        $keys = array_keys($array);
        foreach ($keys as $key) {
            if (is_int($key)) {
                return false;
            }
        }
        return true;
    }
}
