<?php
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
