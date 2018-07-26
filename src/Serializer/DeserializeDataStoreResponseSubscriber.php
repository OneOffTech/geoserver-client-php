<?php

namespace OneOffTech\GeoServer\Serializer;

use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;

/**
 * Pre-Deserialize event for @see \OneOffTech\GeoServer\Model\Error\Error
 *
 * It make sure that data before deserialization into the Error class
 * is in the expected format
 */
class DeserializeDataStoreResponseSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            array(
                'event' => 'serializer.pre_deserialize',
                'method' => 'onPreDeserialize',
                'class' => 'OneOffTech\\GeoServer\\Models\\DataStore',
                'format' => 'json',
                'priority' => 0,
            ),
            array(
                'event' => 'serializer.pre_deserialize',
                'method' => 'onPreDeserialize',
                'class' => 'OneOffTech\\GeoServer\\Http\\Responses\\DataStoreResponse',
                'format' => 'json',
                'priority' => 0,
            ),
        );
    }

    public function onPreDeserialize(PreDeserializeEvent $event)
    {
        $data = $event->getData();

        // The DataStoreResponse has an annoying multi-level
        // array. This aims at flatten the array to 
        // a single level
        if (isset($data['dataStores']) && is_array($data['dataStores'])) {
            $data['dataStores'] = array_map(function($a){
                return isset($a[0]) ? $a[0] : $a;
            },array_values($data['dataStores']));
        }

        // The DataStore has an annoying dataStore key that contain
        // the details. This aims at remove that key when 
        // deserializing a Models\DataStore instance
        if (isset($data['dataStore']) && is_array($data['dataStore'])) {
            $data = $data['dataStore'];
        }

        // The DataStore contain a reference to the workspace by
        // name and url. For the purpose of keeping the object
        // simple we transform the complex object into string
        if (isset($data['workspace']) && is_array($data['workspace'])) {
            $data['workspace'] = $data['workspace']['name'];
        }

        $event->setData($data);

        return true;
    }
}
