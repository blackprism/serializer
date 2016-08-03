<?php
declare(strict_types=1);

namespace tests\units\Blackprism\Serializer\Json;

use Blackprism\Serializer\Configuration;
use Blackprism\Serializer\Exception\InvalidObject;
use Blackprism\Serializer\Value\ClassName;
use tests\fixtures\City;
use tests\fixtures\Country;

class Serialize extends \atoum
{
    public function testSerialize()
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
            ->string($this->testedInstance->serialize($city))
                ->isIdenticalTo('{"name":"Palaiseau"}');
    }

    public function testSerializeWithTypeObject()
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
            ->string($this->testedInstance->serialize($city))
                ->isIdenticalTo('{"name":"Palaiseau","country":{"name":"France"}}');
    }

    public function testSerializeWithTypeObjectAndNullValue()
    {
        $city = new City();
        $city->setName('Palaiseau');
        $city->setZipCode(null);

        $configuration = new Configuration();

        $configurationObject = new Configuration\Object(new ClassName(City::class));
        $configurationObject
            ->attributeUseMethod('name', 'setName', 'getName')
            ->attributeUseMethod('zipCode', 'setZipCode', 'getZipCode')
            ->registerToConfiguration($configuration);

        $this
            ->given($this->newTestedInstance($configuration))
            ->string($this->testedInstance->serialize($city))
                ->isIdenticalTo('{"name":"Palaiseau"}');
    }

    public function testSerializeWithTypeObjectAndEmptyArray()
    {
        $city = new Country();
        $city->setName('France');
        $city->citiesAre([]);

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
            ->string($this->testedInstance->serialize($city))
                ->isIdenticalTo('{"name":"France"}');
    }

    public function testSerializeWithTypeObjectAndEmptyCountable()
    {
        $city = new Country();
        $city->setName('France');
        $city->citiesInTraversable(new class implements \IteratorAggregate, \Countable {
            public function count()
            {
                return 0;
            }

            public function getIterator()
            {
                return new \ArrayIterator();
            }
        });

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
            ->string($this->testedInstance->serialize($city))
                ->isIdenticalTo('{"name":"France"}');
    }

    public function testSerializeWithTypeCollectionObject()
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
            ->string($this->testedInstance->serialize($country))
                ->isIdenticalTo('{"name":"France","cities":[{"name":"Palaiseau"},{"name":"Paris"}]}');
    }

    public function testSerializeWithTypeIdentifiedObject()
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
            ->string($this->testedInstance->serialize($city))
                ->isIdenticalTo('{"type":"city","name":"Palaiseau","country":{"type":"country","name":"France"}}');
    }

    public function testSerializeWithTypeCollectionIdentifiedObject()
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
            ->string($this->testedInstance->serialize($country))
            ->isIdenticalTo('{"type":"country","name":"France","cities":[{"type":"city","name":"Palaiseau"},{"type":"city","name":"Paris"}]}');
    }

    public function testSerializeWithTypeHandler()
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
                        $country->setName($value['name']);
                        $object->countryIs($country);
                        $object->setName($object->getName() . ' (' . $country->getName() . ')');
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
            ->string($this->testedInstance->serialize($city))
                ->isIdenticalTo('{"name":"Palaiseau","country":{"name":"France (with Handler)"}}');
    }

    public function testSerializeShouldThrowExceptionOnInvalidArgument()
    {

        $configuration = new Configuration();

        $configurationObject = new Configuration\Object(new ClassName(City::class));
        $configurationObject
            ->attributeUseMethod('name', 'setName', 'getName')
            ->registerToConfiguration($configuration);

        $this
            ->given($this->newTestedInstance($configuration))
            ->exception(function () {
                $this->testedInstance->serialize("I'm not an object");
            })
               ->isInstanceOf(InvalidObject::class);
    }

    public function testSerializeWithNullValue()
    {
        $city = new City();
        $city->setName('Palaiseau');
        $city->countryIs(null);

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
            ->string($this->testedInstance->serialize($city))
                ->isIdenticalTo('{"name":"Palaiseau"}');
    }

    public function testSerializeWithEmptyArray()
    {
        $country = new Country();
        $country->setName('France');
        $country->citiesAre([]);

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
            ->string($this->testedInstance->serialize($country))
                ->isIdenticalTo('{"name":"France"}');
    }

    public function testSerializeWithEmptyCountable()
    {
        $country = new Country();
        $country->setName('France');
        $country->citiesInTraversable(new \ArrayObject());

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
            ->string($this->testedInstance->serialize($country))
                ->isIdenticalTo('{"name":"France"}');
    }
}
