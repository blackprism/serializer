<?php

declare(strict_types=1);

namespace Blackprism\Serializer\Configuration;

use Blackprism\Serializer\Configuration;
use Blackprism\Serializer\Configuration\Type\HandlerDeserializer;
use Blackprism\Serializer\Configuration\Type\HandlerSerializer;

/**
 * Blackhole
 */
final class Blackhole implements ObjectInterface
{
    /**
     * @param string $attribute
     * @param string $setter
     * @param string $getter
     *
     * @return ObjectInterface
     */
    public function attributeUseMethod(string $attribute, string $setter, string $getter): ObjectInterface
    {
        return $this;
    }

    /**
     * @param string $attribute
     * @param string $class
     * @param string $setter
     * @param string $getter
     *
     * @return ObjectInterface
     */
    public function attributeUseObject(
        string $attribute,
        string $class,
        string $setter,
        string $getter
    ): ObjectInterface {
        return $this;
    }

    /**
     * @param string $attribute
     * @param string $class
     * @param string $setter
     * @param string $getter
     *
     * @return ObjectInterface
     */
    public function attributeUseCollectionObject(
        string $attribute,
        string $class,
        string $setter,
        string $getter
    ): ObjectInterface {
        return $this;
    }

    /**
     * @param string $attribute
     * @param HandlerDeserializer $deserialize
     * @param HandlerSerializer $serialize
     *
     * @return ObjectInterface
     */
    public function attributeUseHandler(
        string $attribute,
        HandlerDeserializer $deserialize,
        HandlerSerializer $serialize
    ): ObjectInterface {
        return $this;
    }

    /**
     * @param Configuration $configuration
     *
     * @return ObjectInterface
     */
    public function registerToConfiguration(Configuration $configuration): ObjectInterface
    {
        return $this;
    }

    /**
     * @param string $attribute
     *
     * @return Type
     */
    public function getTypeForAttribute(string $attribute): Type
    {
        return new Type\Blackhole();
    }

    /**
     * @return string[]
     */
    public function getAttributes(): array
    {
        return [];
    }
}
