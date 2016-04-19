<?php

declare(strict_types=1);

namespace Blackprism\Serializer;

use Blackprism\Serializer\Exception\InvalidDeserializedValue;
use Blackprism\Serializer\Exception\InvalidSerializedValue;
use Blackprism\Serializer\Value\ClassName;

/**
 * SerializerInterface
 */
interface SerializerInterface
{

    /**
     * Deserialize with class name as root
     *
     * @param string $serialized
     * @param ClassName $className
     *
     * @return Object
     * @throws InvalidSerializedValue
     */
    public function deserialize(string $serialized, ClassName $className);

    /**
     * Serialize with class name as root
     *
     * @param Object $object
     *
     * @return string
     * @throws InvalidDeserializedValue
     */
    public function serialize($object): string;
}
