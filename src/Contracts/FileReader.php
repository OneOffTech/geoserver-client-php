<?php

namespace OneOffTech\GeoServer\Contracts;

use Exception;

abstract class FileReader
{
    protected static function openFile($path)
    {
        if (!(is_readable($path) && is_file($path))) {
            throw new Exception("File [$path] not readable");
        }
        $handle = fopen($path, 'r');
        if (!$handle) {
            throw new Exception("Unable to read [$path] as binary file");
        }

        return $handle;
    }
    
    protected static function openFileBinary($path, $position = 0)
    {
        if (!(is_readable($path) && is_file($path))) {
            throw new Exception("File [$path] not readable");
        }
        $handle = fopen($path, 'rb');
        if (!$handle) {
            throw new Exception("Unable to read [$path] as binary file");
        }

        if ($position > 0) {
            fseek($handle, $position, SEEK_SET);
        }
        return $handle;
    }

    protected static function closeFile($handle)
    {
        if ($handle) {
            fclose($handle);
        }
    }
}
