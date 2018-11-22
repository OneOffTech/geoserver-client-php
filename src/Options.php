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

namespace OneOffTech\GeoServer;

use Http\Client\HttpClient;
use JMS\Serializer\Serializer;
use Http\Message\MessageFactory;
use Http\Client\Common\PluginClient;
use JMS\Serializer\SerializerBuilder;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use OneOffTech\GeoServer\Contracts\Authentication;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use OneOffTech\GeoServer\Serializer\DeserializeBoundingBoxSubscriber;
use OneOffTech\GeoServer\Serializer\DeserializeStyleResponseSubscriber;
use OneOffTech\GeoServer\Serializer\DeserializeDataStoreResponseSubscriber;
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
                new HeaderDefaultsPlugin([
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
                $dispatcher->addSubscriber(new DeserializeStyleResponseSubscriber());
            })
            ->build();
        ;
    }
}
