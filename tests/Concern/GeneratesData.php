<?php

namespace Tests\Concern;

use OneOffTech\GeoServer\Model\Data\Copyright;
use OneOffTech\GeoServer\Model\Data\CopyrightOwner;
use OneOffTech\GeoServer\Model\Data\CopyrightUsage;
use OneOffTech\GeoServer\Model\Data\Data;
use OneOffTech\GeoServer\Model\Data\Author;
use OneOffTech\GeoServer\Model\Data\Uploader;
use OneOffTech\GeoServer\Model\Data\Properties;

trait GeneratesData
{

    /**
     * @param string $sampleUUID
     * @return \OneOffTech\GeoServer\Model\Data\Data
     */
    public function createDataModel($sampleUUID, $url = 'http://example.com/data.txt')
    {
        $date = new \DateTime('2008-07-28T14:47:31Z', new \DateTimeZone('UTC'));

        $data = new Data();
        $data->hash = hash('sha512', 'hash');
        $data->type = 'document';
        $data->url = $url;
        $data->uuid = $sampleUUID;

        $author = new Author();
        $author->name = "An Author Name";
        $author->email = "author@email.com";

        $data->authors = [
            $author
        ];

        $uploader = new Uploader();
        $uploader->name = "Uploader Name";
        $uploader->url = "http://some.profile/";

        $data->uploader = $uploader;

        $data->copyright = new Copyright();
        $data->copyright->owner = new CopyrightOwner();
        $data->copyright->owner->name = 'OneOffTech';
        $data->copyright->owner->website = 'https://oneofftech.xyz/';

        $data->copyright->usage = new CopyrightUsage();
        $data->copyright->usage->short = 'MPL-2.0';
        $data->copyright->usage->name = 'Mozilla Public License 2.0';
        $data->copyright->usage->reference = 'https://spdx.org/licenses/MPL-2.0.html';

        $data->properties = new Properties();
        $data->properties->title = 'Adventures of Sherlock Holmes';
        $data->properties->filename = 'adventures-of-sherlock-holmes.pdf';
        $data->properties->mime_type = 'application/pdf';
        $data->properties->language = 'en';
        $data->properties->created_at = $date;
        $data->properties->updated_at = $date;
        $data->properties->size = 150;
        $data->properties->abstract = 'It is a novel about a detective';
        $data->properties->thumbnail = 'https://ichef.bbci.co.uk/news/660/cpsprodpb/153B4/production/_89046968_89046967.jpg';
        $data->properties->tags = ['tag1', 'tag2'];
        $data->properties->collections = ['c1', 'c2'];

        return $data;
    }

    /**
     * @param string $sampleUUID
     * @return array
     */
    public function createDataArray($dataUUID)
    {
        $date = new \DateTime('2008-07-28T14:47:31Z', new \DateTimeZone('UTC'));

        return [
            'hash' => hash('sha512', 'hash'),
            'type' => 'document',
            'url' => 'http://example.com/data.txt',
            'uuid' => $dataUUID,
            'copyright' => [
                'owner' => [
                    'name' => 'KLink Organization',
                    'email' => 'info@klink.asia',
                    'contact' => 'KLink Website: http://www.klink.asia',
                ],
                'usage' => [
                    'short' => 'MPL-2.0',
                    'name' => 'Mozilla Public License 2.0',
                    'reference' => 'https://spdx.org/licenses/MPL-2.0.html',
                ],
            ],
            'properties' => [
                'title' => 'Adventures of Sherlock Holmes',
                'filename' => 'adventures-of-sherlock-holmes.pdf',
                'mime_type' => 'application/pdf',
                'language' => 'en',
                'created_at' => $date,
                'updated_at' => $date,
                'size' => 150,
                'abstract' => 'It is a novel about a detective',
                'thumbnail' => 'https://ichef.bbci.co.uk/news/660/cpsprodpb/153B4/production/_89046968_89046967.jpg',
            ],
        ];
    }
}
