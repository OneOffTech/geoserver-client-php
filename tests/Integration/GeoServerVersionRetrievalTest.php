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

namespace Tests\Integration;

use Tests\TestCase;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Tests\Concern\SetupIntegrationTest;
use OneOffTech\GeoServer\Exception\InvalidDataException;
use OneOffTech\GeoServer\Exception\ErrorResponseException;

class GeoServerVersionRetrievalTest extends TestCase
{
    use SetupIntegrationTest;

    public function test_geoserver_version_is_retrieved()
    {
        $version = $this->geoserver->version();

        $this->assertNotEmpty($version);
        $this->assertTrue(is_string($version));
    }
}
