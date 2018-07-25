<?php

namespace OneOffTech\GeoServer;

use OneOffTech\GeoServer\Contracts\Authentication;

use JMS\Serializer\Serializer;
// use JMS\Serializer\EventDispatcher\EventDispatcher;
use OneOffTech\GeoServer\Exception\AuthTypeNotSupportedException;

use OneOffTech\GeoServer\Auth\NullAuthentication;

use OneOffTech\GeoServer\Http\Routes;
use Psr\Http\Message\ResponseInterface;
use JMS\Serializer\Exception\Exception as JMSException;
use OneOffTech\GeoServer\Http\RequestFactory;
use OneOffTech\GeoServer\Serializer\DeserializeErrorEventSubscriber;
use OneOffTech\GeoServer\Http\InteractsWithHttp;

final class GeoServer
{
    use InteractsWithHttp;

    /** @var  Routes */
    private $routes;
    
    /**
     * Create a GeoServer client instance
     *
     * @param string $url
     * @param string $workspace
     */
    public function __construct($url, $workspace, Options $options)
    {
        $this->httpClient = $options->httpClient;
        $this->messageFactory = $options->messageFactory;
        $this->routes = new Routes($url);
        // $this->apiRequestFactory = new RequestFactory;
        $this->serializer = $options->serializer;
    }

    /**
     *
     * @return string
     */
    public function version()
    {
        $route = $this->routes->url('about/version');

        $response = $this->get($route);

        $geoserver = $response->about->resource[0];

        return "$geoserver->Version";
    }



    /**
     * Build a GeoServer client
     *
     * @param string $url The GeoServer instance URL
     * @param string $workspace The GeoServer workspace to use
     * @param \OneOffTech\GeoServer\Contracts\Authentication $authentication The authentication credentials, if necessary
     * @return GeoServer
     */
    public static function build($url, $workspace, Authentication $authentication = null)
    {
        $options = new Options($authentication ?? (new NullAuthentication));

        return new self($url, $workspace, $options);
    }
}
