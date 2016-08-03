<?php

declare(strict_types=1);

namespace Blackprism\Serializer\Configuration;

use Blackprism\Serializer\Configuration;
use Blackprism\Serializer\Configuration\Type;
use Blackprism\Serializer\Value;

/**
 * Blackhole
 */
final class Blackhole implements ObjectInterface
{
    /**
     * @return Value\ClassName
     */
    public function getClassName(): Value\ClassName
    {
        return new Value\ClassName(Value\Blackhole::class);
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return '';
    }

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
     * @param Value\ClassName $className
     * @param string $setter
     * @param string $getter
     *
     * @return ObjectInterface
     */
    public function attributeUseObject(
        string $attribute,
        Value\ClassName $className,
        string $setter,
        string $getter
    ): ObjectInterface {
        return $this;
    }

    /**
     * @param string $attribute
     * @param Value\ClassName $className
     * @param string $setter
     * @param string $getter
     *
     * @return ObjectInterface
     */
    public function attributeUseCollectionObject(
        string $attribute,
        Value\ClassName $className,
        string $setter,
        string $getter
    ): ObjectInterface {
        return $this;
    }

    /**
     * @param string $attribute
     * @param string $setter
     * @param string $getter
     *
     * @return ObjectInterface
     */
    public function attributeUseIdentifiedObject(string $attribute, string $setter, string $getter): ObjectInterface
    {
        return $this;
    }

    /**
     * @param string $attribute
     * @param string $setter
     * @param string $getter
     *
     * @return ObjectInterface
     */
    public function attributeUseCollectionIdentifiedObject(
        string $attribute,
        string $setter,
        string $getter
    ): ObjectInterface {
        return $this;
    }

    /**
     * @param string $attribute
     * @param Type\HandlerDeserializerInterface $deserialize
     * @param Type\HandlerSerializerInterface $serialize
     *
     * @return ObjectInterface
     */
    public function attributeUseHandler(
        string $attribute,
        Type\HandlerDeserializerInterface $deserialize,
        Type\HandlerSerializerInterface $serialize
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
     * @param Configuration $configuration
     * @param string $identifier
     *
     * @return ObjectInterface
     */
    public function registerToConfigurationWithIdentifier(
        Configuration $configuration,
        string $identifier
    ): ObjectInterface {
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
