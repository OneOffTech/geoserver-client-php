<?php
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
