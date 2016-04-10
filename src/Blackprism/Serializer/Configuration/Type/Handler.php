<?php

declare(strict_types=1);

namespace Blackprism\Serializer\Configuration\Type;

use Blackprism\Serializer\Configuration\Type;

/**
 * Handler
 *
 * @property callable $deserializer
 * @property callable $serializer
 */
final class Handler implements Type
{
    /**
     * @var callable
     */
    private $deserializer;

    /**
     * @var callable
     */
    private $serializer;

    /**
     * @param callable $deserializer
     * @param callable $serializer
     */
    public function __construct(callable $deserializer, callable $serializer)
    {
        $this->deserializer = $deserializer;
        $this->serializer = $serializer;
    }

    /**
     * @return callable
     */
    public function deserializer(): callable
    {
        return $this->deserializer;
    }

    /**
     * @return callable
     */
    public function serializer(): callable
    {
        return $this->serializer;
    }
}
