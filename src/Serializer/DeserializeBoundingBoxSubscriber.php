<?php

namespace OneOffTech\GeoServer\Serializer;

use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;

/**
 * Pre-Deserialize event for @see \OneOffTech\GeoServer\Models\BoundingBox
 *
 * It make sure that data before deserialization into the BoundingBox class
 * is in the expected format
 */
class DeserializeBoundingBoxSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            array(
                'event' => 'serializer.pre_deserialize',
                'method' => 'onPreDeserialize',
                'class' => 'OneOffTech\\GeoServer\\Models\\BoundingBox',
                'format' => 'json',
                'priority' => 0,
            ),
        );
    }

    public function onPreDeserialize(PreDeserializeEvent $event)
    {
        $data = $event->getData();

        // Convert a projected CSR response to string

        if (isset($data['crs']) && !is_string($data['crs'])) {
            $crs = $data['crs'];
            
            if (isset($crs['@class']) && $crs['@class'] === 'projected') {
                $data['crs'] = $crs['$'];
            }
        }

        $event->setData($data);

        return true;
    }
}
