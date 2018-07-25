<?php
namespace Tests\Unit;

use Tests\TestCase;
use OneOffTech\GeoServer\GeoServer;
use OneOffTech\GeoServer\Http\RequestFactory;
use OneOffTech\GeoServer\Auth\Authentication;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use JMS\Serializer\SerializerBuilder;
use Http\Mock\Client as HttpMockClient;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Http\Discovery\MessageFactoryDiscovery;

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
