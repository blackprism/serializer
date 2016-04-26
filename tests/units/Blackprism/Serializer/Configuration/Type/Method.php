<?php

declare(strict_types=1);

namespace tests\units\Blackprism\Serializer\Configuration\Type;

use Blackprism\Serializer\Configuration;
use Blackprism\Serializer\Configuration\Type;
use Blackprism\Serializer\Value\ClassName;
use tests\fixtures\City;
use tests\fixtures\Country;

class Method extends \atoum
{
    public function testSetter()
    {
        $this
            ->given($this->newTestedInstance('setName', 'getName'))
            ->string($this->testedInstance->setter())
                ->isIdenticalTo('setName');
    }

    public function testGetter()
    {
        $this
            ->given($this->newTestedInstance('setName', 'getName'))
            ->string($this->testedInstance->getter())
            ->isIdenticalTo('getName');
    }
}
