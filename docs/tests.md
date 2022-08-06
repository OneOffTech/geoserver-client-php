# Testing

The library is covered with unit and integration tests. 

```
vendor/bin/phpunit
```

By default integration tests are not executed unless in the phpunit.xml file a GeoServer instance is specified.

The `phpunit.xml.dist` define the `GEOSERVER_URL`, `GEOSERVER_USER`,
`GEOSERVER_PASSWORD` for that purpose. 
If you want you can copy `phpunit.xml.dist` to `phpunit.xml` and edit those variables in place 
or define them in your environment variables.

```xml
<env name="GEOSERVER_URL" value="http://localhost:8600/geoserver/"/>
<env name="GEOSERVER_USER" value="user"/>
<env name="GEOSERVER_PASSWORD" value="pass"/>
```

If you don't have a GeoServer instance to trash there is a `docker-compose.yml` file that, with 
the help of Docker and the [kartoza/geoserver image](https://hub.docker.com/r/kartoza/geoserver/), 
creates a running GeoServer instance on port 8600.

> Be aware that the Kartoza GeoServer image requires [4GB of RAM](https://github.com/kartoza/docker-geoserver/blob/master/Dockerfile#L23-L25) to run

```bash
docker-compose -f ./tests/docker-compose.yml up -d
# here better to wait for the full startup of the geoserver
vendor/bin/phpunit
```

**Notes on testing files**

- The GeoPackage testing file was copied from [github.com/ngageoint/geopackage-js](https://github.com/ngageoint/geopackage-js/blob/master/test/fixtures/rivers.gpkg)
