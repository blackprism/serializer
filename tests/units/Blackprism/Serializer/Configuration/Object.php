<?php

declare(strict_types=1);

namespace tests\units\Blackprism\Serializer\Configuration;

use Blackprism\Serializer\Configuration;
use Blackprism\Serializer\Configuration\Type;
use Blackprism\Serializer\Value\ClassName;
use tests\fixtures\City;
use tests\fixtures\Country;

class Object extends \atoum
{
    public function testAttributeUseMethod()
    {
        $this
            ->given($this->newTestedInstance(new ClassName(City::class)))
            ->object($this->testedInstance->attributeUseMethod('name', 'setName', 'getName'))
                ->isIdenticalTo($this->testedInstance);
    }

    public function testAttributeUseObject()
    {
        $this
            ->given($this->newTestedInstance(new ClassName(City::class)))
            ->object($this->testedInstance->attributeUseObject(
                'country',
                new ClassName(Country::class),
                'countryIs',
                'getCountry')
            )
                ->isIdenticalTo($this->testedInstance);
    }

    public function testAttributeUseCollectionObject()
    {
        $this
            ->given($this->newTestedInstance(new ClassName(City::class)))
            ->object($this->testedInstance->attributeUseCollectionObject(
                'country',
                new ClassName(Country::class),
                'countryIs',
                'getCountry')
            )
                ->isIdenticalTo($this->testedInstance);
    }

    public function testAttributeUseHandler()
    {
        $this
            ->given($this->newTestedInstance(new ClassName(City::class)))
            ->object($this->testedInstance->attributeUseHandler(
                'country',
                new class implements Type\HandlerDeserializerInterface {
                    public function deserialize($object, $value)
                    {
                        $country = new Country();
                        $country->setName($value['name']);
                        $object->countryIs($country);
                    }
                },
                new class implements Type\HandlerSerializerInterface {
                    /**
                     * @param City $object
                     * @return mixed
                     */
                    public function serialize($object)
                    {
                        $country = $object->getCountry();
                        return  ['name' => $country->getName()];
                    }
                }))
                ->isIdenticalTo($this->testedInstance);
    }

    public function testRegisterToConfiguration()
    {
        $this
            ->given($this->newTestedInstance(new ClassName(City::class)))
            ->and($configuration = new Configuration())
            ->and($this->testedInstance->registerToConfiguration($configuration))
            ->object($configuration->getConfigurationObjectForClass(new ClassName(City::class)))
                ->isIdenticalTo($this->testedInstance);
    }

    public function testGetTypeForAttribute()
    {
        $method = new Type\Method('setName', 'getName');

        $this
            ->given($this->newTestedInstance(new ClassName(City::class)))
            ->and($this->testedInstance->attributeUseMethod('name', 'setName', 'getName'))
            ->object($this->testedInstance->getTypeForAttribute('name'))
                ->isCloneOf($method);
    }

    public function testGetTypeForAttributeForUnknownAttributeShouldReturnBlackhole()
    {
        $this
            ->given($this->newTestedInstance(new ClassName(City::class)))
            ->object($this->testedInstance->getTypeForAttribute('unknownAttribute'))
            ->isCloneOf(new Type\Blackhole());
    }

    public function testGetAttributes()
    {
        $this
            ->given($this->newTestedInstance(new ClassName(City::class)))
            ->and($this->testedInstance->attributeUseMethod('name', 'setName', 'getName'))
            ->and($this->testedInstance->attributeUseObject(
                'country',
                new ClassName(Country::class),
                'countryIs',
                'getCountry')
            )
            ->array($this->testedInstance->getAttributes())
                ->isIdenticalTo(['name', 'country']);
    }
}
