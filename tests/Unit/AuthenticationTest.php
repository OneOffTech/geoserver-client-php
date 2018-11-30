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
use Psr\Http\Message\RequestInterface;
use OneOffTech\GeoServer\Auth\Authentication;
use OneOffTech\GeoServer\Auth\NullAuthentication;

class AuthenticationTest extends TestCase
{
    public function test_authentication_appends_authorization_header()
    {
        $auth = new Authentication('username', 'password');

        $request = new Request('GET', 'http://geoserver.local');

        $request_with_authentication = $auth->authenticate($request);

        $this->assertInstanceOf(RequestInterface::class, $request_with_authentication);
        $this->assertEquals([sprintf('Basic %s', base64_encode("username:password"))], $request_with_authentication->getHeader('Authorization'));
    }
    
    public function test_null_authentication_do_not_append_authorization_header()
    {
        $auth = new NullAuthentication();

        $request = new Request('GET', 'http://geoserver.local');

        $request_without_authentication = $auth->authenticate($request);

        $this->assertInstanceOf(RequestInterface::class, $request_without_authentication);
        $this->assertEquals(['Host' => ['geoserver.local']], $request_without_authentication->getHeaders());
        $this->assertEmpty($request_without_authentication->getHeader('Authorization'));
    }
}
