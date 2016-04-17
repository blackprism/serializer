<?php

declare(strict_types=1);

namespace Blackprism\Serializer\Configuration\Type;

use Blackprism\Serializer\Configuration\Type;

/**
 * Handler
 *
 * @property HandlerDeserializer $deserializer
 * @property HandlerSerializer $serializer
 */
final class Handler implements Type
{
    /**
     * @var HandlerDeserializer
     */
    private $deserializer;

    /**
     * @var HandlerSerializer
     */
    private $serializer;

    /**
     * @param HandlerDeserializer $deserializer
     * @param HandlerSerializer $serializer
     */
    public function __construct(HandlerDeserializer $deserializer, HandlerSerializer $serializer)
    {
        $this->deserializer = $deserializer;
        $this->serializer = $serializer;
    }

    /**
     * @return HandlerDeserializer
     */
    public function deserializer(): HandlerDeserializer
    {
        return $this->deserializer;
    }

    /**
     * @return HandlerSerializer
     */
    public function serializer(): HandlerSerializer
    {
        return $this->serializer;
    }
}
