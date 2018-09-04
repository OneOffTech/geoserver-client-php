<?php

namespace OneOffTech\GeoServer;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\Plugin\HeaderSetPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use OneOffTech\GeoServer\Contracts\Authentication;
use Http\Message\MessageFactory;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use OneOffTech\GeoServer\Serializer\DeserializeDataStoreResponseSubscriber;
use OneOffTech\GeoServer\Serializer\DeserializeBoundingBoxSubscriber;
use OneOffTech\GeoServer\Serializer\DeserializeCoverageStoreResponseSubscriber;

final class Options
{
    public $authentication;

    /**
     * @var HttpClient
     */
    public $httpClient;

    /**
     * @var MessageFactory
     */
    public $messageFactory;

    /**
     * @var Serializer
     */
    public $serializer;

    const FORMAT_JSON = "application/json";

    /**
     * ...
     */
    public function __construct(Authentication $authentication)
    {
        $this->authentication = $authentication;
        AnnotationRegistry::registerLoader('class_exists');

        // registering a PluginClient as the authentication and
        // some headers should be added to all requests
        $this->httpClient = new PluginClient(
            HttpClientDiscovery::find(),
            [
                new AuthenticationPlugin($this->authentication),
                new HeaderSetPlugin([
                    'User-Agent' => 'OneOffTech GeoServer Client',
                    'Content-Type' => self::FORMAT_JSON,
                    'Accept' => self::FORMAT_JSON
                ]),
            ]
        );

        $this->messageFactory = MessageFactoryDiscovery::find();
        $this->serializer = SerializerBuilder::create()
            ->configureListeners(function (EventDispatcher $dispatcher) {
                $dispatcher->addSubscriber(new DeserializeDataStoreResponseSubscriber());
                $dispatcher->addSubscriber(new DeserializeBoundingBoxSubscriber());
                $dispatcher->addSubscriber(new DeserializeCoverageStoreResponseSubscriber());
            })
            ->build();
        ;
    }
}
