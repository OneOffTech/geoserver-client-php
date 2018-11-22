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

namespace OneOffTech\GeoServer\Http;

use OneOffTech\GeoServer\Support\WmsOptions;

/**
 * Helper class for managing URL creation
 *
 * @internal
 */
final class Routes
{
    /** @var string */
    private $baseUrl;

    /**
     * @param string $baseUrl
     */
    public function __construct($baseUrl)
    {
        $this->baseUrl = trim(trim($baseUrl), '/');
    }

    /**
     * Helper for creating GeoServer Rest URLs
     *
     * @param string $endpoint the endpoint to attach to the base URL
     * @return string
     */
    public function url($endpoint)
    {
        return sprintf("%s/rest/%s", $this->baseUrl, $endpoint);
    }
    
    /**
     * Web Map Service (WMS) service url helper
     *
     * Create the URL to the WMS service based on the specified options
     *
     * @param string $workspace The workspace the URL will refer to
     * @param \OneOffTech\GeoServer\Support\WmsOptions $options The WMS service options
     * @return string
     */
    public function wms($workspace, WmsOptions $options)
    {
        return sprintf(
            "%s/%s/wms?service=WMS&%s",
            $this->baseUrl,
            $workspace,
            $options->toUrlParameters()
        );
    }
}
