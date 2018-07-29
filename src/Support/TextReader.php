<?php

namespace OneOffTech\GeoServer\Support;

use Exception;
use OneOffTech\GeoServer\Contracts\FileReader;

final class TextReader extends FileReader
{
    
    /**
     * Read a line from file
     *
     * @param string $path the file path to read from
     * @param integer $lines the number of lines to read
     * @return array
     */
    public static function readLines($path, $lines = 1)
    {
        $data = [];

        $handle = self::openFile($path);
        for ($i=0; $i < $lines; $i++) {
            $data[] = fgets($handle);
        }
        self::closeFile($handle);

        return $data;
    }
}
