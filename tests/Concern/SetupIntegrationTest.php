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

namespace Tests\Concern;

use OneOffTech\GeoServer\GeoServer;
use OneOffTech\GeoServer\Auth\Authentication;

trait SetupIntegrationTest
{
    /**
     * @var \OneOffTech\GeoServer\GeoServer
     */
    protected $geoserver = null;
    
    protected function setUp(): void
    {
        parent::setUp();

        $url = getenv('GEOSERVER_URL');
        $workspace = getenv('GEOSERVER_WORKSPACE');
        
        if (empty($url)) {
            $this->markTestSkipped('The GEOSERVER_URL is not configured.');
        }
        
        $auth = new Authentication(getenv('GEOSERVER_USER'), getenv('GEOSERVER_PASSWORD'));

        $this->geoserver = GeoServer::build($url, $workspace, $auth);

        $this->geoserver->createWorkspace();
    }
}
