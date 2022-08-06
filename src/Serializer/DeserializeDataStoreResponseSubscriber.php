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
 * Pre-Deserialize event for @see \OneOffTech\GeoServer\Models\DataStore
 *
 * It make sure that data before deserialization into the DataStore class
 * is in the expected format
 */
class DeserializeDataStoreResponseSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            [
                'event' => 'serializer.pre_deserialize',
                'method' => 'onPreDeserialize',
                'class' => 'OneOffTech\\GeoServer\\Models\\DataStore',
                'format' => 'json',
                'priority' => 0,
            ],
            [
                'event' => 'serializer.pre_deserialize',
                'method' => 'onPreDeserialize',
                'class' => 'OneOffTech\\GeoServer\\Http\\Responses\\DataStoreResponse',
                'format' => 'json',
                'priority' => 0,
            ],
        ];
    }

    public function onPreDeserialize(PreDeserializeEvent $event)
    {
        $data = $event->getData();

        // The DataStoreResponse has an annoying multi-level
        // array. This aims at flatten the array to
        // a single level
        if (isset($data['dataStores']) && is_array($data['dataStores'])) {
            $data['dataStores'] = array_map(function ($a) {
                return isset($a[0]) ? $a[0] : $a;
            }, array_values($data['dataStores']));
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
