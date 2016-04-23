<?php

declare(strict_types=1);

namespace Blackprism\Serializer\Configuration;

use Blackprism\Serializer\Configuration;
use Blackprism\Serializer\Configuration\Type\HandlerDeserializerInterface;
use Blackprism\Serializer\Configuration\Type\HandlerSerializerInterface;
use Blackprism\Serializer\Value\ClassName;

/**
 * ObjectInterface
 */
interface ObjectInterface
{

    /**
     * Serialize/Deserialize attribute via method
     *
     * @param string $attribute
     * @param string $setter
     * @param string $getter
     *
     * @return ObjectInterface
     */
    public function attributeUseMethod(string $attribute, string $setter, string $getter): self;

    /**
     * Serialize/Deserialize attribute as an object
     *
     * @param string $attribute
     * @param ClassName $className
     * @param string $setter
     * @param string $getter
     *
     * @return ObjectInterface
     */
    public function attributeUseObject(string $attribute, ClassName $className, string $setter, string $getter): self;

    /**
     * Serialize/Deserialize attribute as an collection of objects
     *
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
    ): self;

    /**
     * Serialize/Deserialize attribute via callable
     *
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
    ): self;

    /**
     * Tell Object to register to Configuration
     *
     * @param Configuration $configuration
     *
     * @return ObjectInterface
     */
    public function registerToConfiguration(Configuration $configuration): self;

    /**
     * Retrieve type for attribute
     *
     * @param string $attribute
     *
     * @return TypeInterface
     */
    public function getTypeForAttribute(string $attribute): TypeInterface;

    /**
     * Retrieve attributes
     *
     * @return string[]
     */
    public function getAttributes(): array;
}
