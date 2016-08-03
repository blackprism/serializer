<?php

declare(strict_types=1);

namespace tests\units\Blackprism\Serializer\Configuration\Type;

use Blackprism\Serializer\Value\ClassName;
use tests\fixtures\City;

class Object extends \atoum
{
    public function testClassName()
    {
        $this
            ->given($this->newTestedInstance(new ClassName(City::class), 'setName', 'getName'))
            ->object($this->testedInstance->className())
                ->isCloneOf(new ClassName(City::class));
    }

    public function testSetter()
    {
        $this
            ->given($this->newTestedInstance(new ClassName(City::class), 'setName', 'getName'))
            ->string($this->testedInstance->setter())
                ->isIdenticalTo('setName');
    }

    public function testGetter()
    {
        $this
            ->given($this->newTestedInstance(new ClassName(City::class), 'setName', 'getName'))
            ->string($this->testedInstance->getter())
            ->isIdenticalTo('getName');
    }
}
