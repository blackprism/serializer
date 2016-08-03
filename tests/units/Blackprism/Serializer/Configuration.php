<?php

declare(strict_types=1);

namespace tests\units\Blackprism\Serializer;

use Blackprism\Serializer\Configuration\Blackhole;
use Blackprism\Serializer\Configuration\Object;
use Blackprism\Serializer\Value\ClassName;
use tests\fixtures\City;

class Configuration extends \atoum
{

    public function testSetterGetterIdentifierAttribute()
    {
        $this
            ->given($this->newTestedInstance)
            ->then($this->testedInstance->identifierAttribute('BlackPrismAttribute'))
            ->string($this->testedInstance->getIdentifierAttribute())
                    ->isIdenticalTo('BlackPrismAttribute');
    }

    public function testAddConfigurationObjectShouldReturnThis()
    {
        $this
            ->given($this->newTestedInstance)
            ->object(
                $this->testedInstance->addConfigurationObject(
                    new ClassName(City::class),
                    new Object(new ClassName(City::class))
                )
            )
            ->isIdenticalTo($this->testedInstance);
    }

    public function testGetConfigurationObjectForClass()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($object = new Object(new ClassName(City::class)))
            ->then($this->testedInstance->addConfigurationObject(new ClassName(City::class), $object))
            ->object($this->testedInstance->getConfigurationObjectForClass(new ClassName(City::class)))
                ->isIdenticalTo($object);
    }

    public function testGetConfigurationObjectForClassShouldReturnBlackhole()
    {
        $this
            ->given($this->newTestedInstance)
            ->object($this->testedInstance->getConfigurationObjectForClass(new ClassName(City::class)))
                ->isInstanceOf(Blackhole::class);
    }

    public function testGetConfigurationObjectForIdentifier()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($identifier = uniqid())
            ->and($object = new Object(new ClassName(City::class)))
            ->then($this->testedInstance
                ->addConfigurationObjectWithIdentifier(new ClassName(City::class), $object, $identifier))
            ->object($this->testedInstance->getConfigurationObjectForIdentifier($identifier))
                ->isIdenticalTo($object);
    }

    public function testGetConfigurationObjectForIdentifierShouldReturnBlackhole()
    {
        $this
            ->given($this->newTestedInstance)
            ->object($this->testedInstance->getConfigurationObjectForIdentifier(uniqid()))
                ->isInstanceOf(Blackhole::class);
    }
}
