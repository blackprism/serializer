<?php

declare(strict_types=1);

namespace Blackprism\Serializer\Configuration;

use Blackprism\Serializer\Configuration;

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
     * @param string $class
     * @param string $setter
     * @param string $getter
     *
     * @return ObjectInterface
     */
    public function attributeUseObject(string $attribute, string $class, string $setter, string $getter): self;

    /**
     * Serialize/Deserialize attribute as an collection of objects
     *
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
    ): self;

    /**
     * Serialize/Deserialize attribute via callable
     *
     * @param string   $attribute
     * @param callable $serialize
     * @param callable $deserialize
     *
     * @return ObjectInterface
     */
    public function attributeUseHandler(string $attribute, callable $serialize, callable $deserialize): self;

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
     * @return Type
     */
    public function getTypeForAttribute(string $attribute): Type;

    /**
     * Retrieve attributes
     *
     * @return string[]
     */
    public function getAttributes(): array;
}
