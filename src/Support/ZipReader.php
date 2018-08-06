<?php

namespace OneOffTech\GeoServer\Support;

use Exception;
use ZipArchive;
use OneOffTech\GeoServer\Contracts\FileReader;

final class ZipReader extends FileReader
{

    
    /**
     * Read a 32 bit integer from the beginning of a file
     *
     * @param string $path the file path to read from
     * @param bool $big_endian if the integer is in big endian notation. Default true
     * @return integer
     */
    public static function contentList($path)
    {
        $entries = [];

        $za = new ZipArchive;
        $za->open($path);

        for ($i=0; $i < $za->numFiles; $i++) {
            $entry = $za->statIndex($i);
            $entries[] = $entry['name'];
        }

        $za->close();

        return $entries;
    }

    public static function containsFile($path, $name)
    {
        $entries = [];

        $za = new ZipArchive;
        $za->open($path);

        for ($i=0; $i < $za->numFiles; $i++) {
            $entry = $za->statIndex($i);
            if (strpos($entry['name'], $name) !== false) {
                $entries[] = $entry['name'];
            }
        }

        $za->close();

        return count($entries) > 0;
    }

    // public static function getFileFrom($path)
    // {
    //     // dump($za->getFromIndex(0));
    // }
}
