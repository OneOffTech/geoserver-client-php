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
use OneOffTech\GeoServer\Serializer\DeserializeErrorEventSubscriber;
use OneOffTech\GeoServer\Http\InteractsWithHttp;
use OneOffTech\GeoServer\Models\Workspace;

use OneOffTech\GeoServer\Http\Responses\WorkspaceResponse;
use OneOffTech\GeoServer\Http\Responses\DataStoreResponse;
use OneOffTech\GeoServer\Models\DataStore;


final class GeoServer
{
    use InteractsWithHttp;

    /** @var string */
    private $workspace;

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
        $this->serializer = $options->serializer;
        $this->workspace = $workspace;
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
     * Retrieve the workspace information.
     * 
     * @uses the workspace specified during client instantiation
     * 
     * @return \OneOffTech\GeoServer\Models\Workspace
     */
    public function workspace()
    {
        $route = $this->routes->url("workspaces/$this->workspace");

        $response = $this->get($route, WorkspaceResponse::class);

        return $response->workspace;
    }

    /**
     * Retrieve the list of datastores defined in the workspace.
     * A data store contains vector format spatial data.
     * 
     * @uses the workspace specified during client instantiation
     * 
     * @return \OneOffTech\GeoServer\Models\DataStore[]
     */
    public function datastores()
    {
        $route = $this->routes->url("workspaces/$this->workspace/datastores");

        $response = $this->get($route, DataStoreResponse::class);

        return $response->dataStores;
    }

    /**
     * Retrieve the details of a data store.
     * A data store contains vector format spatial data. It can be a file (such as a shapefile),...
     * 
     * @uses the workspace specified during client instantiation
     * 
     * @param string $name The data store name
     * @return \OneOffTech\GeoServer\Models\DataStore
     */
    public function datastore($name)
    {
        $route = $this->routes->url("workspaces/$this->workspace/datastores/$name");

        $response = $this->get($route, DataStore::class);

        return $response;
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
