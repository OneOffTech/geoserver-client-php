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

namespace Tests\Unit;

use Tests\TestCase;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use OneOffTech\GeoServer\GeoServer;
use JMS\Serializer\SerializerBuilder;
use Psr\Http\Message\RequestInterface;
use Http\Mock\Client as HttpMockClient;
use Http\Discovery\MessageFactoryDiscovery;
use OneOffTech\GeoServer\Auth\Authentication;
use OneOffTech\GeoServer\Http\RequestFactory;
use Doctrine\Common\Annotations\AnnotationRegistry;

class GeoServerClientInstantiationTest extends TestCase
{
    public function test_client_can_be_created_with_authentication()
    {
        $auth = new Authentication('username', 'password');

        $url = 'https://geoserver.local/';
        $workspace = 'default';

        $client = GeoServer::build($url, $workspace, $auth);

        $this->assertInstanceOf(GeoServer::class, $client);
    }
    
    public function test_client_can_be_created_with_no_authentication()
    {
        $url = 'https://geoserver.local/';
        $workspace = 'default';

        $client = GeoServer::build($url, $workspace);

        $this->assertInstanceOf(GeoServer::class, $client);
    }
}
