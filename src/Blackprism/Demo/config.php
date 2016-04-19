<?php

use Blackprism\Serializer\Configuration;
use Blackprism\Serializer\Value\ClassName;

$configuration = new Configuration();

$configurationObject = new Configuration\Object(new ClassName(Blackprism\Demo\Entity\City::class));
$configurationObject
    ->attributeUseMethod('id', 'setId', 'getId')
    ->attributeUseMethod('name', 'setName', 'getName')
    ->attributeUseObject('country', new ClassName(Blackprism\Demo\Entity\Country::class), 'countryIs', 'getCountry')
    ->attributeUseCollectionObject(
        'countries',
        new ClassName(Blackprism\Demo\Entity\Country::class),
        'countriesAre',
        'getCountries'
    )
    ->registerToConfiguration($configuration);

$configurationObject = new Configuration\Object(new ClassName(Blackprism\Demo\Entity\Country::class));
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
    ->attributeUseObject('city', new ClassName(Blackprism\Demo\Entity\City::class), 'cityIs', 'getCity')
    ->registerToConfiguration($configuration);
