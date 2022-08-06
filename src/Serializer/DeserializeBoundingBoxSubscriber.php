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
        return [
            [
                'event' => 'serializer.pre_deserialize',
                'method' => 'onPreDeserialize',
                'class' => 'OneOffTech\\GeoServer\\Models\\BoundingBox',
                'format' => 'json',
                'priority' => 0,
            ],
        ];
    }

    public function onPreDeserialize(PreDeserializeEvent $event)
    {
        $data = $event->getData();

        // Convert a projected CSR response to string

        if (isset($data['crs']) && ! is_string($data['crs'])) {
            $crs = $data['crs'];
            
            if (isset($crs['@class']) && $crs['@class'] === 'projected') {
                $data['crs'] = $crs['$'];
            }
        }

        $event->setData($data);

        return true;
    }
}
