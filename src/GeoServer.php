<?php

namespace OneOffTech\GeoServer;

use OneOffTech\GeoServer\Http\Routes;
use Psr\Http\Message\ResponseInterface;
use OneOffTech\GeoServer\Models\Feature;
use OneOffTech\GeoServer\Models\DataStore;
use OneOffTech\GeoServer\Models\Workspace;
use OneOffTech\GeoServer\Http\InteractsWithHttp;
use OneOffTech\GeoServer\Auth\NullAuthentication;
use OneOffTech\GeoServer\Contracts\Authentication;
use OneOffTech\GeoServer\Http\Responses\FeatureResponse;
use OneOffTech\GeoServer\Http\Responses\WorkspaceResponse;
use OneOffTech\GeoServer\Http\Responses\DataStoreResponse;
use OneOffTech\GeoServer\Exception\AuthTypeNotSupportedException;

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
     *
     * A feature is a vector based spatial resource or data set that originates from a data store.
     * In some cases, such as with a shapefile, a feature type has a one-to-one relationship with its
     * data store. In other cases, such as PostGIS, the relationship of feature type to data store
     * is many-to-one, feature types corresponding to a table in the database.
     *
     * @param string $datastore The datastore from which retrieve the
     * @param string $name The feature name to retrieve
     * @return \OneOffTech\GeoServer\Models\Feature
     */
    public function feature($datastore, $name = null)
    {
        $feature = $name ?? $datastore;
        $route = $this->routes->url("workspaces/$this->workspace/datastores/$datastore/featuretypes/$feature");

        $response = $this->get($route, FeatureResponse::class);

        return $response->feature;
    }


    /**
     * Upload data to the pertaining store
     *
     * Raster data will be added to a coveragestore
     *
     */
    public function upload(GeoFile $data)
    {
        $route = $this->routes->url("workspaces/$this->workspace/datastores/$data->name/file.{$data->extension}");

        $this->putFile($route, $data);

        return $this->feature($data->name);
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
