<?php

declare(strict_types=1);

namespace Blackprism\Serializer\Configuration\Type;

/**
 * HandlerDeserializer
 */
interface HandlerDeserializer
{
    /**
     * @param Object $object
     * @param mixed $value
     */
    public function deserialize($object, $value);
}
