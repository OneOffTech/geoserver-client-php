<?php

namespace OneOffTech\GeoServer;

use OneOffTech\GeoServer\Http\Routes;
use Psr\Http\Message\ResponseInterface;
use OneOffTech\GeoServer\Models\Feature;
use OneOffTech\GeoServer\Models\DataStore;
use OneOffTech\GeoServer\Models\Workspace;
use OneOffTech\GeoServer\Models\CoverageStore;
use OneOffTech\GeoServer\Http\InteractsWithHttp;
use OneOffTech\GeoServer\Auth\NullAuthentication;
use OneOffTech\GeoServer\Contracts\Authentication;
use OneOffTech\GeoServer\Http\Responses\FeatureResponse;
use OneOffTech\GeoServer\Http\Responses\CoverageResponse;
use OneOffTech\GeoServer\Http\Responses\WorkspaceResponse;
use OneOffTech\GeoServer\Http\Responses\DataStoreResponse;
use OneOffTech\GeoServer\Exception\ErrorResponseException;
use OneOffTech\GeoServer\Exception\StoreNotFoundException;
use OneOffTech\GeoServer\Http\Responses\CoverageStoreResponse;
use OneOffTech\GeoServer\Http\Responses\CoverageStoresResponse;
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
     * Get the GeoServer version
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
     * Create the configured workspace, if not existing
     *
     * @uses the workspace specified during client instantiation
     *
     * @return \OneOffTech\GeoServer\Models\Workspace
     */
    public function createWorkspace()
    {
        try {
            $route = $this->routes->url("workspaces");

            $response = $this->post($route, ['workspace' => [ 'name' => $this->workspace] ]);
        } catch (ErrorResponseException $ex) {
            if ($ex->getCode() !== 401) {
                throw $ex;
            }
        }
        return $this->workspace();
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

        try {
            $response = $this->get($route, DataStore::class);
            
            return $response;
        } catch (ErrorResponseException $ex) {
            if ($ex->getMessage() === 'Not Found') {
                throw StoreNotFoundException::datastore($name);
            }

            throw $ex;
        }
    }

    /**
     * Delete a data store
     *
     * @uses the workspace specified during client instantiation
     *
     * @param string $name The data store name
     * @return \OneOffTech\GeoServer\Models\DataStore
     * @throws \OneOffTech\GeoServer\Exception\StoreNotFoundException if the data store to remove do not exists
     */
    public function deleteDatastore($name)
    {
        $datastore = $this->datastore($name);

        $route = $this->routes->url("workspaces/$this->workspace/datastores/$name?recurse=true");

        $this->delete($route);

        $datastore->exists = false;

        return $datastore;
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
     * Retrieve the list of coverage stores defined in the workspace.
     * A data store contains raster format spatial data.
     *
     * @uses the workspace specified during client instantiation
     *
     * @return \OneOffTech\GeoServer\Models\CoverageStore[]
     */
    public function coveragestores()
    {
        $route = $this->routes->url("workspaces/$this->workspace/coveragestores");

        $response = $this->get($route, CoverageStoresResponse::class);

        return $response->stores;
    }

    /**
     * Retrieve the details of a coverage store.
     * A coverage store describes how access a raster data source.
     *
     * @uses the workspace specified during client instantiation
     *
     * @param string $name The coverage store name
     * @return \OneOffTech\GeoServer\Models\CoverageStore
     */
    public function coveragestore($name)
    {
        $route = $this->routes->url("workspaces/$this->workspace/coveragestores/$name");

        try {
            $response = $this->get($route, CoverageStoreResponse::class);
            
            return $response->store;
        } catch (ErrorResponseException $ex) {
            if ($ex->getMessage() === 'Not Found') {
                throw StoreNotFoundException::coveragestore($name);
            }

            throw $ex;
        }
    }

    /**
     * Retrieve the details of a coverage.
     * A coverage is a raster data set which originates from a coverage store.
     *
     * @uses the workspace specified during client instantiation
     *
     * @param string $coveragestore The coverage store from which retrieve the coverage
     * @param string $name The coverage name to retrieve
     * @return \OneOffTech\GeoServer\Models\Coverage
     */
    public function coverage($coveragestore, $name = null)
    {
        $coverage = $name ?? $coveragestore;
        $route = $this->routes->url("workspaces/$this->workspace/coveragestores/$coveragestore/coverages/$coverage");

        $response = $this->get($route, CoverageResponse::class);

        return $response->coverage;
    }

    /**
     * Delete a coverage store
     *
     * @uses the workspace specified during client instantiation
     *
     * @param string $name The coverage store name
     * @return \OneOffTech\GeoServer\Models\CoverageStore
     * @throws \OneOffTech\GeoServer\Exception\StoreNotFoundException if the coverage store to remove do not exists
     */
    public function deleteCoveragestore($name)
    {
        $coveragestore = $this->coveragestore($name);

        $route = $this->routes->url("workspaces/$this->workspace/coveragestores/$name?recurse=true&purge=all");

        $this->delete($route);

        $coveragestore->exists = false;

        return $coveragestore;
    }


    /**
     * Upload a file to the pertaining store
     *
     * Vector data will be added to a data store
     * Raster data will be added to a coverage store
     *
     * @param GeoFile $file
     * @return \OneOffTech\GeoServer\Models\Feature|\OneOffTech\GeoServer\Models\Coverage The information about the uploaded coverage or raster feature
     */
    public function upload(GeoFile $file)
    {
        $store = GeoType::storeFor($file->type);
        $route = $this->routes->url("workspaces/$this->workspace/$store/$file->name/file.{$file->normalizedExtension}");

        $this->putFile($route, $file);

        if(GeoType::VECTOR === $file->type){
            return $this->feature($file->name);
        }

        return $this->coverage($file->name);
    }

    /**
     * Delete a GeoFile from the GeoServer instance
     * 
     * Deletes the corresponding store based on the GeoType format
     * 
     * @param GeoFile $data The GeoFile to delete
     * @return bool
     * @throws \OneOffTech\GeoServer\Exception\StoreNotFoundException if the store, that corresponds to the file, do not exists
     */
    public function remove(GeoFile $data)
    {
        if($data->type === GeoType::VECTOR){
            $this->deleteDatastore($data->name);
            return true;
        }
        else if($data->type === GeoType::RASTER){
            $this->deleteCoveragestore($data->name);
            return true;
        }

        return false;
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
