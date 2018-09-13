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

    /**
     * Tap into the Zip Archive
     *
     * After the callback is executed the ZIP archive is closed and saved
     *
     * @param string The zip file path
     * @param callable The function to execute when the zip file is opened. This function receive a ZipArchive instance as argument
     * @return string The zip file path
     */
    public static function tap($path, $callback)
    {
        $za = new ZipArchive;
        $za->open($path);

        try {
            $callback($za);
        } catch (Exception $ex) {
            throw $ex;
        } finally {
            $za->close();
        }

        return $path;
    }
}
