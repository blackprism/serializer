<?php

declare(strict_types=1);

namespace Blackprism\Serializer\Configuration\Type;

/**
 * HandlerSerializer
 */
interface HandlerSerializer
{
    /**
     * @param Object $object
     *
     * @return mixed
     */
    public function serialize($object);
}
