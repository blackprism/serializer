<?php
declare(strict_types=1);

namespace tests\units\Blackprism\Serializer\Json;

use Blackprism\Serializer\Configuration;
use Blackprism\Serializer\Exception\InvalidJson;
use Blackprism\Serializer\Exception\MissingIdentifierAttribute;
use Blackprism\Serializer\Exception\UndefinedIdentifierAttribute;
use Blackprism\Serializer\Value\ClassName;
use tests\fixtures\City;
use tests\fixtures\Country;

class Deserialize extends \atoum
{
    public function testDeserialize()
    {
        $city = new City();
        $city->setName('Palaiseau');

        $configuration = new Configuration();

        $configurationObject = new Configuration\Object(new ClassName(City::class));
        $configurationObject
            ->attributeUseMethod('name', 'setName', 'getName')
            ->registerToConfiguration($configuration);

        $this
            ->given($this->newTestedInstance($configuration))
            ->object($this->testedInstance->deserialize('{"name": "Palaiseau"}', new ClassName(City::class)))
                ->isCloneOf($city);
    }

    public function testDeserializeWithTypeObject()
    {
        $city = new City();
        $city->setName('Palaiseau');

        $country = new Country();
        $country->setName('France');
        $city->countryIs($country);

        $configuration = new Configuration();

        $configurationObject = new Configuration\Object(new ClassName(City::class));
        $configurationObject
            ->attributeUseMethod('name', 'setName', 'getName')
            ->attributeUseObject('country', new ClassName(Country::class), 'countryIs', 'getCountry')
            ->registerToConfiguration($configuration);

        $configurationObject = new Configuration\Object(new ClassName(Country::class));
        $configurationObject
            ->attributeUseMethod('name', 'setName', 'getName')
            ->registerToConfiguration($configuration);

        $this
            ->given($this->newTestedInstance($configuration))
            ->object($this->testedInstance->deserialize(
                '{"name": "Palaiseau", "country": {"name": "France"}}',
                new ClassName(City::class))
            )
                ->isCloneOf($city);
    }

    public function testDeserializeWithTypeCollectionObject()
    {
        $cityPalaiseau = new City();
        $cityPalaiseau->setName('Palaiseau');

        $cityParis = new City();
        $cityParis->setName('Paris');

        $country = new Country();
        $country->setName('France');
        $country->citiesAre([$cityPalaiseau, $cityParis]);

        $configuration = new Configuration();

        $configurationObject = new Configuration\Object(new ClassName(Country::class));
        $configurationObject
            ->attributeUseMethod('name', 'setName', 'getName')
            ->attributeUseCollectionObject('cities', new ClassName(City::class), 'citiesAre', 'getCities')
            ->registerToConfiguration($configuration);

        $configurationObject = new Configuration\Object(new ClassName(City::class));
        $configurationObject
            ->attributeUseMethod('name', 'setName', 'getName')
            ->registerToConfiguration($configuration);

        $this
            ->given($this->newTestedInstance($configuration))
            ->object($this->testedInstance->deserialize(
                '{"name": "France","cities":[ {"name":"Palaiseau"}, {"name":"Paris"} ]}',
                new ClassName(Country::class)
            ))
            ->isCloneOf($country);
    }

    public function testDeserializeWithTypeIdentifiedObject()
    {
        $city = new City();
        $city->setName('Palaiseau');

        $country = new Country();
        $country->setName('France');
        $city->countryIs($country);

        $configuration = new Configuration();
        $configuration->identifierAttribute('type');

        $configurationObject = new Configuration\Object(new ClassName(City::class));
        $configurationObject
            ->attributeUseMethod('name', 'setName', 'getName')
            ->attributeUseIdentifiedObject('country', 'countryIs', 'getCountry')
            ->registerToConfigurationWithIdentifier($configuration, 'city');

        $configurationObject = new Configuration\Object(new ClassName(Country::class));
        $configurationObject
            ->attributeUseMethod('name', 'setName', 'getName')
            ->registerToConfigurationWithIdentifier($configuration, 'country');

        $this
            ->given($this->newTestedInstance($configuration))
            ->object($this->testedInstance->deserialize(
                '{"type": "city", "name": "Palaiseau", "country": {"type": "country", "name": "France"}}'
            ))
            ->isCloneOf($city);
    }

    public function testDeserializeWithTypeIdentifiedObjectShouldNotObjectWithoutIdentifierAttribute()
    {
        $city = new City();
        $city->setName('Palaiseau');

        $configuration = new Configuration();
        $configuration->identifierAttribute('type');

        $configurationObject = new Configuration\Object(new ClassName(City::class));
        $configurationObject
            ->attributeUseMethod('name', 'setName', 'getName')
            ->attributeUseIdentifiedObject('country', 'countryIs', 'getCountry')
            ->registerToConfigurationWithIdentifier($configuration, 'city');

        $configurationObject = new Configuration\Object(new ClassName(Country::class));
        $configurationObject
            ->attributeUseMethod('name', 'setName', 'getName')
            ->registerToConfigurationWithIdentifier($configuration, 'country');

        $this
            ->given($this->newTestedInstance($configuration))
            ->object($this->testedInstance->deserialize(
                '{"type": "city", "name": "Palaiseau", "country": {"name": "France"}}'
            ))
                ->isCloneOf($city);
    }

    public function testDeserializeWithTypeIdentifiedObjectShouldNotObjectWithUnknownIdentifierAttribute()
    {
        $city = new City();
        $city->setName('Palaiseau');

        $configuration = new Configuration();
        $configuration->identifierAttribute('type');

        $configurationObject = new Configuration\Object(new ClassName(City::class));
        $configurationObject
            ->attributeUseMethod('name', 'setName', 'getName')
            ->attributeUseIdentifiedObject('country', 'countryIs', 'getCountry')
            ->registerToConfigurationWithIdentifier($configuration, 'city');

        $configurationObject = new Configuration\Object(new ClassName(Country::class));
        $configurationObject
            ->attributeUseMethod('name', 'setName', 'getName')
            ->registerToConfigurationWithIdentifier($configuration, 'country');

        $this
            ->given($this->newTestedInstance($configuration))
            ->object($this->testedInstance->deserialize(
                '{"type": "city", "name": "Palaiseau", "country": {"type": "unknownType", "name": "France"}}'
            ))
                ->isCloneOf($city);
    }

    public function testDeserializeWithTypeCollectionIdentifiedObject()
    {
        $cityPalaiseau = new City();
        $cityPalaiseau->setName('Palaiseau');

        $cityParis = new City();
        $cityParis->setName('Paris');

        $country = new Country();
        $country->setName('France');
        $country->citiesAre([$cityPalaiseau, $cityParis]);

        $configuration = new Configuration();
        $configuration->identifierAttribute('type');

        $configurationObject = new Configuration\Object(new ClassName(Country::class));
        $configurationObject
            ->attributeUseMethod('name', 'setName', 'getName')
            ->attributeUseCollectionIdentifiedObject('cities', 'citiesAre', 'getCities')
            ->registerToConfigurationWithIdentifier($configuration, 'country');

        $configurationObject = new Configuration\Object(new ClassName(City::class));
        $configurationObject
            ->attributeUseMethod('name', 'setName', 'getName')
            ->registerToConfigurationWithIdentifier($configuration, 'city');

        $this
            ->given($this->newTestedInstance($configuration))
            ->object($this->testedInstance->deserialize(
                '{"type": "country", "name": "France","cities":[ 
                    {"type": "city", "name":"Palaiseau"}, {"type": "city", "name":"Paris"} 
                ]}',
                new ClassName(Country::class)
            ))
            ->isCloneOf($country);
    }

    public function testDeserializeWithTypeCollectionIdentifiedObjectShouldNotObjectWithoutIdentifierAttribute()
    {
        $cityPalaiseau = new City();
        $cityPalaiseau->setName('Palaiseau');

        $country = new Country();
        $country->setName('France');
        $country->citiesAre([$cityPalaiseau]);

        $configuration = new Configuration();
        $configuration->identifierAttribute('type');

        $configurationObject = new Configuration\Object(new ClassName(Country::class));
        $configurationObject
            ->attributeUseMethod('name', 'setName', 'getName')
            ->attributeUseCollectionIdentifiedObject('cities', 'citiesAre', 'getCities')
            ->registerToConfigurationWithIdentifier($configuration, 'country');

        $configurationObject = new Configuration\Object(new ClassName(City::class));
        $configurationObject
            ->attributeUseMethod('name', 'setName', 'getName')
            ->registerToConfigurationWithIdentifier($configuration, 'city');

        $this
            ->given($this->newTestedInstance($configuration))
            ->object($this->testedInstance->deserialize(
                '{"type": "country", "name": "France","cities":[ 
                    {"type": "city", "name":"Palaiseau"}, {"name":"Paris"} 
                ]}',
                new ClassName(Country::class)
            ))
                ->isCloneOf($country);
    }

    public function testDeserializeWithTypeCollectionIdentifiedObjectShouldNotProcessSubObjectWithUnknownIdentifierAttribute()
    {
        $cityPalaiseau = new City();
        $cityPalaiseau->setName('Palaiseau');

        $country = new Country();
        $country->setName('France');
        $country->citiesAre([$cityPalaiseau]);

        $configuration = new Configuration();
        $configuration->identifierAttribute('type');

        $configurationObject = new Configuration\Object(new ClassName(Country::class));
        $configurationObject
            ->attributeUseMethod('name', 'setName', 'getName')
            ->attributeUseCollectionIdentifiedObject('cities', 'citiesAre', 'getCities')
            ->registerToConfigurationWithIdentifier($configuration, 'country');

        $configurationObject = new Configuration\Object(new ClassName(City::class));
        $configurationObject
            ->attributeUseMethod('name', 'setName', 'getName')
            ->registerToConfigurationWithIdentifier($configuration, 'city');

        $this
            ->given($this->newTestedInstance($configuration))
            ->object($this->testedInstance->deserialize(
                '{"type": "country", "name": "France","cities":[ 
                    {"type": "city", "name":"Palaiseau"}, {"type": "unknownType", "name":"Paris"} 
                ]}',
                new ClassName(Country::class)
            ))
            ->isCloneOf($country);
    }

    public function testDeserializeWithTypeHandler()
    {
        $city = new City();
        $city->setName('Palaiseau');
        $country = new Country();
        $country->setName('France');
        $city->countryIs($country);

        $configuration = new Configuration();

        $configurationObject = new Configuration\Object(new ClassName(City::class));
        $configurationObject
            ->attributeUseMethod('name', 'setName', 'getName')
            ->attributeUseHandler(
                'country',
                new class implements Configuration\Type\HandlerDeserializerInterface {
                    public function deserialize($object, $value)
                    {
                        $country = new Country();
                        $country->setName(str_replace(' (Handler please remove me)', '', $value['name']));
                        $object->countryIs($country);
                        $object->setName(
                            str_replace(' (Handler please remove me if i have a country)', '', $object->getName())
                        );
                    }
                },
                new class implements Configuration\Type\HandlerSerializerInterface {
                    public function serialize($object)
                    {
                        $country = $object->getCountry();
                        return  ['name' => $country->getName() . ' (with Handler)' ];
                    }
                }
            )
            ->registerToConfiguration($configuration);

        $configurationObject = new Configuration\Object(new ClassName(Country::class));
        $configurationObject
            ->attributeUseMethod('name', 'setName', 'getName')
            ->registerToConfiguration($configuration);

        $this
            ->given($this->newTestedInstance($configuration))
            ->object($this->testedInstance->deserialize(
                '{"name":"Palaiseau (Handler please remove me if i have a country)","country":{"name":"France (Handler please remove me)"}}',
                new ClassName(City::class)
            ))
                ->isCloneOf($city);
    }

    public function testDeserializeShouldThrowExceptionOnInvalidJson()
    {
        $configuration = new Configuration();

        $configurationObject = new Configuration\Object(new ClassName(City::class));
        $configurationObject
            ->attributeUseMethod('name', 'setName', 'getName')
            ->registerToConfiguration($configuration);

        $this
            ->given($this->newTestedInstance($configuration))
            ->exception(function () {
                $this->testedInstance->deserialize('"name": "Palaiseau"', new ClassName(City::class));
            })
               ->isInstanceOf(InvalidJson::class);
    }

    public function testDeserializeForIdentifiedObjectShouldThrowExceptionWhenIdentifierAttributeNotSetOnMainObject()
    {
        $configuration = new Configuration();

        $this
            ->given($this->newTestedInstance($configuration))
            ->exception(function () {
                $this->testedInstance->deserialize('{"name": "Palaiseau"}');
            })
                ->isInstanceOf(UndefinedIdentifierAttribute::class);
    }

    public function testDeserializeForIdentifiedObjectShouldThrowExceptionWhenJsonDontHaveIdentifier()
    {
        $configuration = new Configuration();
        $configuration->identifierAttribute('type');

        $configurationObject = new Configuration\Object(new ClassName(City::class));
        $configurationObject
            ->attributeUseMethod('name', 'setName', 'getName')
            ->registerToConfigurationWithIdentifier($configuration, 'city');

        $this
            ->given($this->newTestedInstance($configuration))
            ->exception(function () {
                $this->testedInstance->deserialize('{"name": "Palaiseau"}');
            })
                ->isInstanceOf(MissingIdentifierAttribute::class);
    }
}
