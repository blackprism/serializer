<?php

declare(strict_types=1);

namespace tests\units\Blackprism\Serializer\Value;

use tests\fixtures\City;

class ClassName extends \atoum
{
    public function testConstructThrowErrorOnInvalidClass()
    {
        $this
            ->exception(function () {
                new \Blackprism\Serializer\Value\ClassName('InvalidClassName');
            })
                ->isInstanceOf(\InvalidArgumentException::class)
                ->hasMessage('InvalidClassName not found');
    }

    public function testGetValue()
    {
        $this
            ->given($this->newTestedInstance(City::class))
            ->string($this->testedInstance->getValue())
                ->isIdenticalTo(City::class);
    }

    public function testGetIdentifier()
    {
        $this
            ->given($this->newTestedInstance(City::class))
            ->string($this->testedInstance->getIdentifier())
                ->isIdenticalTo(City::class);
    }
}
