<?php

require(__DIR__ . '/../vendor/autoload.php');

use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Client\Exception\RequestException;

$messageFactory = MessageFactoryDiscovery::find();
$httpClient = HttpClientDiscovery::find();

$route = rtrim(getenv('GEOSERVER_URL'), '/') . '/rest/about/version.json'; //rtrim(getenv('GEOSERVER_URL'), '/')

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
