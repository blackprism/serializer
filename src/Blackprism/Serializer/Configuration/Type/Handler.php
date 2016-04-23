<?php

declare(strict_types=1);

namespace Blackprism\Serializer\Configuration\Type;

use Blackprism\Serializer\Configuration\TypeInterface;

/**
 * Handler
 *
 * @property HandlerDeserializerInterface $deserializer
 * @property HandlerSerializerInterface $serializer
 */
final class Handler implements TypeInterface
{
    /**
     * @var HandlerDeserializerInterface
     */
    private $deserializer;

    /**
     * @var HandlerSerializerInterface
     */
    private $serializer;

    /**
     * @param HandlerDeserializerInterface $deserializer
     * @param HandlerSerializerInterface $serializer
     */
    public function __construct(HandlerDeserializerInterface $deserializer, HandlerSerializerInterface $serializer)
    {
        $this->deserializer = $deserializer;
        $this->serializer = $serializer;
    }

    /**
     * @return HandlerDeserializerInterface
     */
    public function deserializer(): HandlerDeserializerInterface
    {
        return $this->deserializer;
    }

    /**
     * @return HandlerSerializerInterface
     */
    public function serializer(): HandlerSerializerInterface
    {
        return $this->serializer;
    }
}
