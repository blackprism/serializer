<?php

declare(strict_types=1);

namespace Blackprism\Serializer;

use Blackprism\Serializer\Exception\InvalidSerializedValue;
use Blackprism\Serializer\Value\ClassName;

/**
 * SerializerInterface
 */
interface DeserializerInterface
{

    /**
     * Deserialize with class name as root
     *
     * @param string $serialized
     * @param ClassName $className
     *
     * @return object|object[]
     * @throws InvalidSerializedValue
     */
    public function deserialize(string $serialized, ClassName $className);
}
