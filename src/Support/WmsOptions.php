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

namespace OneOffTech\GeoServer\Support;

use LogicException;
use ReflectionClass;
use InvalidArgumentException;
use OneOffTech\GeoServer\Models\BoundingBox;

/**
 * Options for the Web Map Service (WMS)
 *
 * Helper class to create the parameters for the WMS service call
 */
final class WmsOptions
{
    /**
     * Output format for PNG image
     */
    const OUTPUT_PNG = "image/png";

    /**
     * Same as PNG, but computes an optimal 256 color (8 bit) palette, so the image size is usually smaller
     */
    const OUTPUT_PNG8 = "image/png8";

    /**
     *
     */
    const OUTPUT_JPEG = "image/jpeg";

    /**
     * A custom format that will decide dynamically, based on the image contents, if it’s best to use a JPEG or PNG compression. The images are returned in JPEG format if fully opaque and not paletted. In order to use this format in a meaningful way the GetMap must include a “&transparent=TRUE” parameter, as without it GeoServer generates opaque images with the default/requested background color, making this format always return JPEG images (or always PNG, if they are paletted). When using the layer preview to test this format, remember to add “&transparent=TRUE” to the preview URL, as normally the preview generates non transparent images.
     */
    const OUTPUT_JPEG_PNG = "image/vnd.jpeg-png";

    /**
     *
     */
    const OUTPUT_GIF = "image/gif";

    /**
     *
     */
    const OUTPUT_TIFF = "image/tiff";

    /**
     * Same as TIFF, but computes an optimal 256 color (8 bit) palette, so the image size is usually smaller
     */
    const OUTPUT_TIFF8 = "image/tiff8";

    /**
     * Same as TIFF, but includes extra GeoTIFF metadata
     */
    const OUTPUT_GEOTIFF = "image/geotiff";

    /**
     * Same as TIFF, but includes extra GeoTIFF metadata and computes an optimal 256 color (8 bit) palette, so the image size is usually smaller
     */
    const OUTPUT_GEOTIFF8 = "image/geotiff8";

    /**
     *
     */
    const OUTPUT_SVG = "image/svg";

    /**
     *
     */
    const OUTPUT_PDF = "application/pdf";

    /**
     *
     */
    const OUTPUT_GEORSS = "rss";

    /**
     *
     */
    const OUTPUT_KML = "kml";

    /**
     *
     */
    const OUTPUT_KMZ = "kmz";

    /**
     * Generates an OpenLayers HTML application.
     */
    const OUTPUT_OPENLAYERS = "application/openlayers";

    /**
     * Generates an UTFGrid 1.3 JSON response. Requires vector output, either from a vector layer, or from a raster layer turned into vectors by a rendering transformation.
     */
    const OUTPUT_UTFGRID = "application/json;type=utfgrid";

    private $format = self::OUTPUT_PNG;
    
    private $bbox = null;
    
    private $layers = null;
    
    private $styles = null;
    
    private $width = 640;
    
    private $height = 480;
    
    private $srs = "EPSG:4326";

    private $version = "1.1.0";

    private $request = "GetMap";

    private function isFormatValid($format)
    {
        return in_array($format, $this->supportedFormats());
    }

    public function supportedFormats()
    {
        $constants = (new ReflectionClass(get_called_class()))->getConstants();

        return array_values($constants);
    }

    public function format($format)
    {
        if (! $this->isFormatValid($format)) {
            throw new InvalidArgumentException("Unrecognized format [$format] Expected one of [".join(",", $this->supportedFormats())."]");
        }
        
        $this->format = $format;
        return $this;
    }

    public function srs($srs)
    {
        $this->srs = $srs;
        return $this;
    }
    
    public function layers($layers)
    {
        $this->layers = is_array($layers) ? $layers : [$layers];
        return $this;
    }
    
    public function styles($styles)
    {
        $this->styles = is_array($styles) ? $styles : [$styles];
        return $this;
    }
    
    public function size($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
        return $this;
    }
    
    public function boundingBox(BoundingBox $boundingBox)
    {
        $this->bbox = $boundingBox;
        return $this;
    }

    public function toArray()
    {
        if (empty($this->layers)) {
            throw new LogicException("Layers cannot be null or empty");
        }
        
        if (is_null($this->bbox)) {
            throw new LogicException("Bounding box cannot be null");
        }

        return [
            'request' => $this->request,
            'version' => $this->version,
            'format' => $this->format,
            'layers' => $this->layers,
            'bbox' => $this->bbox->toArray(),
            'srs' => $this->srs,
            'width' => $this->width,
            'height' => $this->height,
            'styles' => $this->styles ?? [],
        ];
    }

    public function toUrlParameters()
    {
        $params = [
            'version' => $this->version,
            'request' => $this->request,
            'layers' => join(',', $this->layers),
            'bbox' => join(',', $this->bbox->toArray()),
            'styles' => join(',', $this->styles ?? []),
            'width' => $this->width,
            'height' => $this->height,
            'srs' => $this->srs,
            'format' => urlencode($this->format),
        ];

        $collapsedParams = array_map(function ($key, $value) {
            return "$key=$value";
        }, array_keys($params), $params);

        return join("&", $collapsedParams);
    }
}
