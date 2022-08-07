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

use Exception;
use OneOffTech\GeoServer\Http\Routes;
use OneOffTech\GeoServer\Models\Style;
use OneOffTech\GeoServer\Models\Feature;
use OneOffTech\GeoServer\Models\Resource;
use OneOffTech\GeoServer\Models\DataStore;
use OneOffTech\GeoServer\Models\Workspace;
use OneOffTech\GeoServer\Support\ZipReader;
use OneOffTech\GeoServer\Support\WmsOptions;
use OneOffTech\GeoServer\Http\InteractsWithHttp;
use OneOffTech\GeoServer\Auth\NullAuthentication;
use OneOffTech\GeoServer\Contracts\Authentication;
use OneOffTech\GeoServer\Http\Responses\StylesResponse;
use OneOffTech\GeoServer\Http\Responses\FeatureResponse;
use OneOffTech\GeoServer\Http\Responses\CoverageResponse;
use OneOffTech\GeoServer\Exception\ErrorResponseException;
use OneOffTech\GeoServer\Exception\StoreNotFoundException;
use OneOffTech\GeoServer\Exception\StyleNotFoundException;
use OneOffTech\GeoServer\Http\Responses\DataStoreResponse;
use OneOffTech\GeoServer\Http\Responses\WorkspaceResponse;
use OneOffTech\GeoServer\Http\Responses\CoverageStoreResponse;
use OneOffTech\GeoServer\Exception\StyleAlreadyExistsException;
use OneOffTech\GeoServer\Http\Responses\CoverageStoresResponse;

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
            if ($ex->getCode() !== 401 && $ex->getCode() !== 409) {
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
     * @return \OneOffTech\GeoServer\Models\Resource The resource that was uploaded. Can be a Coverage for raster data or Feature for vector data
     */
    public function upload(GeoFile $file)
    {
        $store = GeoType::storeFor($file->type);
        $route = $this->routes->url("workspaces/$this->workspace/$store/$file->name/file.{$file->normalizedExtension}?charset=UTF-8");

        // if ZIP shpefile and name was changed from original filename
        // we need to rename all files contained in it
        if ($file->format === GeoFormat::SHAPEFILE_ZIP && $file->wasRenamed()) {
            $contentList = ZipReader::contentList($file->path());
            $nameWithoutExtension = rtrim($file->name, $file->extension);
            
            // check the content if modifications are required
            if (! in_array("$nameWithoutExtension.shp", $contentList)) {

                // if yes, clone the content and do the filename edits to
                // make sure that files inside the zip are correctly named
                $copy = $file->copy();
                
                ZipReader::tap($copy->path(), function ($zip) use ($contentList, $nameWithoutExtension) {
                    foreach ($contentList as $entry) {
                        $entryExtension = pathinfo($entry, PATHINFO_EXTENSION);
                        $newEntryName = "$nameWithoutExtension.$entryExtension";
                        $zip->renameName($entry, $newEntryName);
                    }
                });

                try {
                    $this->putFile($route, $copy);
                } catch (Exception $ex) {
                    throw $ex;
                } finally {
                    unlink($copy->path());
                }
            } else {
                $this->putFile($route, $file);
            }
        } else {
            $this->putFile($route, $file);
        }

        return $this->details($file);
    }

    public function details(GeoFile $file)
    {
        if (GeoType::VECTOR === $file->type) {
            return $this->feature($file->name);
        }

        return $this->coverage($file->name);
    }

    /**
     * Check if a specified GeoFile was uploaded to the Geoserver
     * The check will attempt to find the store that matches the given name
     *
     * @param GeoFile $file
     * @return bool
     */
    public function exist(GeoFile $file)
    {
        $store = $file->type === GeoType::VECTOR ? 'feature' : 'coverage';
        
        try {
            $found = $this->{$store}($file->name);
    
            if (! is_null($found)) {
                return true;
            }
    
            return false;
        } catch (Exception $ex) {
            return false;
        }
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
        if ($data->type === GeoType::VECTOR) {
            $this->deleteDatastore($data->name);
            return true;
        } elseif ($data->type === GeoType::RASTER) {
            $this->deleteCoveragestore($data->name);
            return true;
        }

        return false;
    }

    /**
     * Get the Web Map Service (WMS) map URL for the specified resource
     *
     * @param GeoFile|Resource data the data you want to obtain the WMS url for. If a GeoFile is passed, the corresponding resource is retrieved from the geoserver, if found
     * @param WmsOption $wmsOptions The options to configure the WMS output
     */
    public function wmsMapUrl($data, ?WmsOptions $wmsOptions = null)
    {
        if (! ($data instanceof GeoFile || $data instanceof Resource)) {
            throw new InvalidArgumentException("Data must be a GeoFile or Resource instance.");
        }

        $resource = $data instanceof Resource ? $data : $this->details($data);

        $options = $wmsOptions ?? (new WmsOptions())
            ->layers("$this->workspace:$resource->name")
            ->boundingBox($resource->boundingBox)
            ->srs($resource->boundingBox->crs ?? $resource->srs);

        return $this->routes->wms($this->workspace, $options);
    }

    /**
     * Attempt to retrieve a thumbnail of a previously uploaded
     * GeoFile or Resource using the Web Map Service
     *
     * @param GeoFile|Resource data the data you want to obtain the WMS url for. If a GeoFile is passed, the corresponding resource is retrieved from the geoserver, if found
     * @return resource A resource
     */
    public function thumbnail($data, $width = 300, $height = 300)
    {
        if (! ($data instanceof GeoFile || $data instanceof Resource)) {
            throw new InvalidArgumentException("Data must be a GeoFile or Resource instance.");
        }

        $resource = $data instanceof Resource ? $data : $this->details($data);

        $url = $this->wmsMapUrl($resource, (new WmsOptions())
            ->layers("$this->workspace:$resource->name")
            ->boundingBox($resource->boundingBox)
            ->size($width, $height)
            ->srs($resource->boundingBox->crs ?? $resource->srs));
        
        return $this->getImage($url);
    }

    /**
     * Get a style by its name.
     * The style must be in the current workspace
     *
     * @param string $name
     * @return \OneOffTech\GeoServer\Models\Style
     * @throws StyleNotFoundException if the style with the given name cannot be found
     * @throws ErrorResponseException for communication errors with the GeoServer
     */
    public function style($name)
    {
        $route = $this->routes->url("workspaces/$this->workspace/styles/$name");

        try {
            $response = $this->get($route, Style::class);
            return $response;
        } catch (ErrorResponseException $ex) {
            if ($ex->getMessage() === 'Not Found') {
                throw StyleNotFoundException::style($name, $this->workspace);
            }

            throw $ex;
        }
    }
    
    /**
     * Get all the styles defined in the current workspace
     *
     * @return \OneOffTech\GeoServer\Models\Style[]
     */
    public function styles()
    {
        $route = $this->routes->url("workspaces/$this->workspace/styles");

        $response = $this->get($route, StylesResponse::class);

        $eagerLoaded = array_map(function ($style) {
            return $this->style($style->name);
        }, $response->styles);

        return $eagerLoaded;
    }

    /**
     * Upload a SLD style to the workspace
     *
     * @param StyleFile $file The style file
     * @return \OneOffTech\GeoServer\Models\Style the uploaded style details
     * @throws StyleAlreadyExistsException
     */
    public function uploadStyle(StyleFile $file)
    {
        $filePostRoute = $this->routes->url("workspaces/$this->workspace/styles");

        try {
            $postFileResponse = $this->postFile($filePostRoute.'?name='.$file->name, $file);
        } catch (ErrorResponseException $ex) {
            // we receive a 500 error response if the style already exists
            if ($ex->getData() === "Style named '$file->name' already exists in workspace $this->workspace") {
                throw StyleAlreadyExistsException::style($file->name, $this->workspace);
            }
            if (strpos($ex->getData(), "Style $file->name already exists") !== false) {
                throw StyleAlreadyExistsException::style($file->name, $this->workspace);
            }

            throw $ex;
        }

        return $this->style($file->name);
    }

    /**
     * Remove a style from the workspace, given its name
     *
     * @param string $name the name of the style to remove. It must be in the workspace
     * @return \OneOffTech\GeoServer\Models\Style
     * @throws StyleNotFoundException if the style with the given name cannot be found
     * @throws ErrorResponseException for communication errors with the GeoServer
     */
    public function removeStyle($name)
    {
        $style = $this->style($name);

        $route = $this->routes->url("workspaces/$this->workspace/styles/$name");

        $this->delete($route);

        $style->exists = false;

        return $style;
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
