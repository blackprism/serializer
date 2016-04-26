<?php

declare(strict_types=1);

namespace tests\units\Blackprism\Serializer;

use Blackprism\Serializer\Configuration\Blackhole;
use Blackprism\Serializer\Configuration\Object;
use Blackprism\Serializer\Value\ClassName;
use tests\fixtures\City;

class Configuration extends \atoum
{
    public function testAddConfigurationObjectShouldReturnThis()
    {
        $this
            ->given($this->newTestedInstance)
            ->object($this->testedInstance->addConfigurationObject(
                new ClassName(City::class),
                new Object(new ClassName(City::class)))
            )
                ->isIdenticalTo($this->testedInstance);
    }

    public function testGetConfigurationObjectForClassS()
    {
        $this
            ->given($this->newTestedInstance)
            ->then($object = new Object(new ClassName(City::class)))
            ->then($this->testedInstance->addConfigurationObject(new ClassName(City::class), $object)
            )
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
}
