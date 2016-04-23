<?php

declare(strict_types=1);

namespace Blackprism\Serializer\Configuration;

use Blackprism\Serializer\Configuration;
use Blackprism\Serializer\Configuration\Type\HandlerDeserializerInterface;
use Blackprism\Serializer\Configuration\Type\HandlerSerializerInterface;
use Blackprism\Serializer\Value\ClassName;

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
     * @param ClassName $className
     * @param string $setter
     * @param string $getter
     *
     * @return ObjectInterface
     */
    public function attributeUseObject(
        string $attribute,
        ClassName $className,
        string $setter,
        string $getter
    ): ObjectInterface {
        return $this;
    }

    /**
     * @param string $attribute
     * @param ClassName $className
     * @param string $setter
     * @param string $getter
     *
     * @return ObjectInterface
     */
    public function attributeUseCollectionObject(
        string $attribute,
        ClassName $className,
        string $setter,
        string $getter
    ): ObjectInterface {
        return $this;
    }

    /**
     * @param string $attribute
     * @param HandlerDeserializerInterface $deserialize
     * @param HandlerSerializerInterface $serialize
     *
     * @return ObjectInterface
     */
    public function attributeUseHandler(
        string $attribute,
        HandlerDeserializerInterface $deserialize,
        HandlerSerializerInterface $serialize
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
     * @return TypeInterface
     */
    public function getTypeForAttribute(string $attribute): TypeInterface
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
