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

require __DIR__.'/../vendor/autoload.php';

use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Client\Exception\RequestException;

$messageFactory = MessageFactoryDiscovery::find();
$httpClient = HttpClientDiscovery::find();

$route = 'http://127.0.0.1:8600/geoserver/rest/about/version.html';

$request = $messageFactory->createRequest('GET', $route, []);

$start = time();

while (true) {
    try {
        $response = $httpClient->sendRequest($request);

        if ($response->getStatusCode() === 200 || $response->getStatusCode() === 401) {
            fwrite(STDOUT, 'Docker container started!'.PHP_EOL);
            exit(0);
        }
    } catch (RequestException $exception) {
        $elapsed = time() - $start;

        if ($elapsed > 30) {
            fwrite(STDERR, 'Docker container did not start in time...'.PHP_EOL);
            exit(1);
        }

        fwrite(STDOUT, 'Waiting for container to start...'.PHP_EOL);
        sleep(1);
    }
}
