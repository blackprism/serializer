<?php

declare(strict_types=1);

namespace Blackprism\Serializer;

use Blackprism\Serializer\Exception\InvalidDeserializedValue;

/**
 * SerializerInterface
 */
interface SerializerInterface
{

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
