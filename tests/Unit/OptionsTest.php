<?php
namespace Tests\Unit;

use Tests\TestCase;
use Http\Client\HttpClient;
use JMS\Serializer\Serializer;
use Http\Message\MessageFactory;
use OneOffTech\GeoServer\Options;
use Http\Client\Common\PluginClient;
use OneOffTech\GeoServer\Auth\NullAuthentication;

class OptionsTest extends TestCase
{
    public function test_options_contains_expected_configuration()
    {
        $auth = new NullAuthentication();

        $options = new Options($auth);

        $this->assertInstanceOf(PluginClient::class, $options->httpClient, "http client is not instantiated");
        $this->assertInstanceOf(HttpClient::class, $options->httpClient, "http client is not instantiated");
        $this->assertEquals($auth, $options->authentication, "authentication not set");
        $this->assertInstanceOf(MessageFactory::class, $options->messageFactory, "message factory is not instantiated");
        $this->assertInstanceOf(Serializer::class, $options->serializer, "serializer is not instantiated");
    }
}
