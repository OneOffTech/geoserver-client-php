<?php

namespace OneOffTech\GeoServer\Serializer;

use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;

/**
 * Pre-Deserialize event for @see \OneOffTech\GeoServer\Models\CoverageStore
 *
 * It make sure that data before deserialization into the CoverageStore class
 * is in the expected format
 */
class DeserializeCoverageStoreResponseSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            array(
                'event' => 'serializer.pre_deserialize',
                'method' => 'onPreDeserialize',
                'class' => 'OneOffTech\\GeoServer\\Http\\Responses\\CoverageStoresResponse',
                'format' => 'json',
                'priority' => 1,
            ),
            array(
                'event' => 'serializer.pre_deserialize',
                'method' => 'onPreDeserialize',
                'class' => 'OneOffTech\\GeoServer\\Models\\CoverageStore',
                'format' => 'json',
                'priority' => 0,
            ),
        );
    }

    public function onPreDeserialize(PreDeserializeEvent $event)
    {
        $data = $event->getData();

        // The CoverageStoreResponse has an annoying multi-level
        // array. This aims at flatten the array to
        // a single level
        if (isset($data['coverageStores']) && is_array($data['coverageStores'])) {
            $data['coverageStores'] = array_map(function ($a) {
                return isset($a[0]) ? $a[0] : $a;
            }, array_values($data['coverageStores']));
        }

        // The CoverageStore contain a reference to the workspace by
        // name and url. For the purpose of keeping the object
        // simple we transform the complex object into string
        if (isset($data['workspace']) && is_array($data['workspace'])) {
            $data['workspace'] = $data['workspace']['name'];
        }

        if (isset($data['coverages']) && is_string($data['coverages'])) {
            $data['coverages'] = [$data['coverages']];
        }

        $event->setData($data);

        return true;
    }
}
