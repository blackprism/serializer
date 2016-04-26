<?php

declare(strict_types=1);

namespace tests\units\Blackprism\Serializer\Configuration\Type;

use Blackprism\Serializer\Configuration;
use Blackprism\Serializer\Configuration\Type;
use Blackprism\Serializer\Value\ClassName;
use tests\fixtures\City;
use tests\fixtures\Country;

class Handler extends \atoum
{
    private $deserializer;
    private $serializer;

    public function beforeTestMethod($testMethod)
    {
        $this->deserializer = new class implements Configuration\Type\HandlerDeserializerInterface {
            public function deserialize($object, $value)
            {
                $country = new Country();
                $country->setName($value['name']);
                $object->countryIs($country);
            }
        };

        $this->serializer = new class implements Configuration\Type\HandlerSerializerInterface {
            public function serialize($object)
            {
                $country = $object->getCountry();
                return  ['name' => $country->getName()];
            }
        };
    }

    public function testDeserializer()
    {
        $this
            ->given($this->newTestedInstance($this->deserializer, $this->serializer))
            ->object($this->testedInstance->deserializer())
                ->isIdenticalTo($this->deserializer);
    }

    public function testSerializer()
    {
        $this
            ->given($this->newTestedInstance($this->deserializer, $this->serializer))
            ->object($this->testedInstance->serializer())
                ->isIdenticalTo($this->serializer);
    }
}
