<?php
/*
 *    GeoServer PHP Client
 *
 *    Copyright (c) 2018 Oneoff-tech UG, Germany, www.oneofftech.xyz
 *
 *    This program is Free Software: you can redistribute it and/or modify
 *    it under the terms of the GNU Affero General Public License as
 *    published by the Free Software Foundation, either version 3 of the
 *    License, or (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public
 *    License along with this program.  If not, see
 *    <http://www.gnu.org/licenses/>.
 */

namespace Tests\Support;

use InvalidArgumentException;

class ImageDifference
{

    /**
     * Calculate the difference between two images
     *
     * @param string $expected The path of the image ground truth
     * @param string $actual The actual image to compare
     * @return float the percentage of difference between the two images
     */
    public static function calculate($expected, $actual)
    {
        list($expectedImage, $expectedImagewidth, $expectedImageHeight) = static::imageFromFile($expected);
        
        list($actualImage) = static::imageFromFile($actual);

        $differenceBitmap = static::calculateDifference(
            $expectedImage,
            $actualImage,
            $expectedImagewidth,
            $expectedImageHeight
        );

        return static::calculateDifferencePercentage($differenceBitmap, $expectedImagewidth, $expectedImageHeight);
    }

    /**
     * Load a bitmap array from image path.
     *
     * @param string $path
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    private static function imageFromFile($path)
    {
        $info = getimagesize($path);
        $type = $info[2];

        $image = null;

        if ($type == IMAGETYPE_JPEG) {
            $image = imagecreatefromjpeg($path);
        }
        if ($type == IMAGETYPE_GIF) {
            $image = imagecreatefromgif($path);
        }
        if ($type == IMAGETYPE_PNG) {
            $image = imagecreatefrompng($path);
        }

        if (! $image) {
            throw new InvalidArgumentException("invalid image [{$path}]");
        }

        $width = imagesx($image);
        $height = imagesy($image);

        $bitmap = [];

        for ($y = 0; $y < $height; $y++) {
            $bitmap[$y] = [];

            for ($x = 0; $x < $width; $x++) {
                $color = imagecolorat($image, $x, $y);

                $bitmap[$y][$x] = [
                    "r" => ($color >> 16) & 0xFF,
                    "g" => ($color >> 8) & 0xFF,
                    "b" => $color & 0xFF
                ];
            }
        }

        return [$bitmap, $width, $height];
    }

    /**
     * Difference between all pixels of two images.
     *
     * @param array $bitmap1
     * @param array $bitmap2
     * @param int $width
     * @param int $height
     *
     * @return array
     */
    private static function calculateDifference(array $bitmap1, array $bitmap2, $width, $height)
    {
        $new = [];

        for ($y = 0; $y < $height; $y++) {
            $new[$y] = [];

            for ($x = 0; $x < $width; $x++) {
                $new[$y][$x] = static::euclideanDistance(
                    $bitmap1[$y][$x],
                    $bitmap2[$y][$x]
                );
            }
        }

        return $new;
    }

    /**
     * RGB color distance for the same pixel in two images.
     *
     * @link https://en.wikipedia.org/wiki/Euclidean_distance
     *
     * @param array $p
     * @param array $q
     *
     * @return float
     */
    private static function euclideanDistance(array $p, array $q)
    {
        $r = $p["r"] - $q["r"];
        $r *= $r;

        $g = $p["g"] - $q["g"];
        $g *= $g;

        $b = $p["b"] - $q["b"];
        $b *= $b;

        return (float) sqrt($r + $g + $b);
    }

    /**
     * Percentage of different pixels in the bitmap.
     *
     * @param array $bitmap
     * @param int $width
     * @param int $height
     *
     * @return float
     */
    private static function calculateDifferencePercentage(array $bitmap, $width, $height)
    {
        $total = 0;
        $different = 0;

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $total++;

                if ($bitmap[$y][$x] > 0) {
                    $different++;
                }
            }
        }

        return (float) (($different / $total) * 100);
    }
}
