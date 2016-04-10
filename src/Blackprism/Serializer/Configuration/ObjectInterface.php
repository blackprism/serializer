<?php

declare(strict_types=1);

namespace Blackprism\Serializer\Configuration;

use Blackprism\Serializer\Configuration;

/**
 * Class ObjectInterface
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
     * @return $this
     */
    public function attributeUseMethod(string $attribute, string $setter, string $getter);

    /**
     * Serialize/Deserialize attribute as an object
     *
     * @param string $attribute
     * @param string $class
     * @param string $setter
     * @param string $getter
     *
     * @return $this
     */
    public function attributeUseObject(string $attribute, string $class, string $setter, string $getter);

    /**
     * Serialize/Deserialize attribute as an collection of objects
     *
     * @param string $attribute
     * @param string $class
     * @param string $setter
     * @param string $getter
     *
     * @return $this
     */
    public function attributeUseCollectionObject(string $attribute, string $class, string $setter, string $getter);

    /**
     * Serialize/Deserialize attribute via callable
     *
     * @param string   $attribute
     * @param callable $serialize
     * @param callable $deserialize
     *
     * @return $this
     */
    public function attributeUseHandler(string $attribute, callable $serialize, callable $deserialize);

    /**
     * Tell Object to register to Configuration
     *
     * @param Configuration $configuration
     *
     * @return $this
     */
    public function registerToConfiguration(Configuration $configuration);

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
