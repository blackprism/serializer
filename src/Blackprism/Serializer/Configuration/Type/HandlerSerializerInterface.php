<?php

declare(strict_types=1);

namespace Blackprism\Serializer\Configuration\Type;

/**
 * HandlerSerializerInterface
 */
interface HandlerSerializerInterface
{
    /**
     * @param object $object
     *
     * @return mixed
     */
    public function serialize($object);
}
