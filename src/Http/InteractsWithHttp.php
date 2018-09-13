<?php

namespace OneOffTech\GeoServer\Http;

use Exception;
use Throwable;
use Http\Client\HttpClient;
use Http\Message\MessageFactory;
use Psr\Http\Message\ResponseInterface;
use OneOffTech\GeoServer\Http\ResponseHelper;
use OneOffTech\GeoServer\Support\ImageResponse;
use OneOffTech\GeoServer\Exception\InvalidDataException;
use OneOffTech\GeoServer\Exception\ErrorResponseException;
use OneOffTech\GeoServer\Exception\SerializationException;
use OneOffTech\GeoServer\Exception\DeserializationException;

trait InteractsWithHttp
{

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var MessageFactory
     */
    private $messageFactory;
    
    /**
     * @var Serializer
     */
    private $serializer;
    

    /**
     * @param ResponseInterface $response
     * @throws ErrorResponseException
     */
    private function checkResponseError(ResponseInterface $response)
    {
        $responseBody = $response->getBody();
        $contentTypeHeader = $response->getHeader('Content-Type');
        $contentType = !empty($contentTypeHeader) ? $contentTypeHeader[0] : '';
        
        if ($response->getStatusCode() !== 200 && $response->getStatusCode() !== 201 && $response->getStatusCode() !== 204) {
            // dump($response);
            if ($response->getStatusCode() === 500 && strpos($contentType, 'text/html')!== false) {
                $reason = substr((string)$responseBody, 0, 500);

                throw new ErrorResponseException($reason, $response->getStatusCode(), (string)$responseBody);
            }
            throw new ErrorResponseException(!empty($response->getReasonPhrase()) ? $response->getReasonPhrase() : 'There was a problem in fulfilling your request.', $response->getStatusCode(), (string)$responseBody);
        }
    }

    /**
     * Deserialize a JSON string into the given class instance
     *
     * @param string $json the JSON string to deserialized
     * @param string $class the fully qualified class name
     * @return object instance of $class
     * @throws DeserializationException if an error occurs during the deserialization
     */
    protected function deserialize($response, $class = null)
    {
        // dump(json_decode($response->getBody()));
        if (is_null($class)) {
            return json_decode($response->getBody());
        }

        try {
            return $this->serializer->deserialize($response->getBody(), $class, 'json');
        } catch (JMSException $ex) {
            throw new DeserializationException($ex->getMessage(), (string)$response->getBody());
        }
    }

    protected function serialize($object)
    {
        try {
            return $this->serializer->serialize($object, 'json');
        } catch (Throwable $ex) {
            throw new SerializationException($ex->getMessage());
        } catch (Exception $ex) {
            throw new SerializationException($ex->getMessage());
        }
    }

    /**
     * Handle and send the request to the given route.
     *
     * @param RPCRequest $request The request data
     * @param string     $route   The API route
     *
     * @return ResponseInterface
     * @throws SerializationException
     */
    private function handleRequest($request)
    {
        $response = $this->httpClient->sendRequest($request);
        
        $this->checkResponseError($response);

        return $response;
    }
    
    protected function get($route, $class = null)
    {
        $request = $this->messageFactory->createRequest('GET', $route, []);

        $response = $this->handleRequest($request);

        return $this->deserialize($response, $class);
    }

    protected function post($route, $data, $class = null)
    {
        $request = $this->messageFactory->createRequest('POST', $route, [], $this->serialize($data));

        $response = $this->handleRequest($request);

        return $this->deserialize($response, $class);
    }

    protected function put($route, $data, $class = null)
    {
        $request = $this->messageFactory->createRequest('PUT', $route, [], $this->serialize($data));

        $response = $this->handleRequest($request);

        return $this->deserialize($response, $class);
    }
    
    protected function putFile($route, $data, $class = null)
    {
        $request = $this->messageFactory->createRequest('PUT', $route, ['Content-Type' => $data->normalizedMimeType], $data->content());

        $response = $this->handleRequest($request);

        return $this->deserialize($response, $class);
    }

    protected function delete($route, $class = null)
    {
        $request = $this->messageFactory->createRequest('DELETE', $route, []);

        $response = $this->handleRequest($request);

        return $this->deserialize($response, $class);
    }

    protected function getImage($route)
    {
        $request = $this->messageFactory->createRequest('GET', $route, []);

        $response = $this->handleRequest($request);

        $contentTypeHeader = $response->getHeader('Content-Type');
        $contentType = !empty($contentTypeHeader) ? $contentTypeHeader[0] : '';

        if ($response->getStatusCode() !== 200 && $response->getStatusCode() !== 201 && $response->getStatusCode() !== 204) {
            throw new ErrorResponseException(!empty($response->getReasonPhrase()) ? $response->getReasonPhrase() : 'There was a problem in fulfilling your request.', $response->getStatusCode(), (string)$responseBody);
        }

        if (strpos($contentType, 'image') === false) {
            throw new ErrorResponseException("Expected image response, but got [$contentType]", $response->getStatusCode(), (string)$response->getBody());
        }

        return ImageResponse::from($response);
    }
}
