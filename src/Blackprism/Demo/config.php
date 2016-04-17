<?php

use Blackprism\Serializer\Configuration;

$configuration = new Configuration();

$configurationObject = new Configuration\Object(Blackprism\Demo\Entity\City::class);
$configurationObject
    ->attributeUseMethod('id', 'setId', 'getId')
    ->attributeUseMethod('name', 'setName', 'getName')
    ->attributeUseObject('country', Blackprism\Demo\Entity\Country::class, 'countryIs', 'getCountry')
    ->attributeUseCollectionObject('countries', Blackprism\Demo\Entity\Country::class, 'countriesAre', 'getCountries')
    ->registerToConfiguration($configuration);

$configurationObject = new Configuration\Object(Blackprism\Demo\Entity\Country::class);
$configurationObject
    ->attributeUseMethod('code', 'setCode', 'getCode')
    ->attributeUseHandler(
        'name',
        new class implements Configuration\Type\HandlerDeserializer {
            public function deserialize($object, $value)
            {
                $object->setName($value);
            }
        },
        new class implements Configuration\Type\HandlerSerializer {
            public function serialize($object)
            {
                return $object->getName();
            }
        })
    ->attributeUseObject('city', Blackprism\Demo\Entity\City::class, 'cityIs', 'getCity')
    ->registerToConfiguration($configuration);
