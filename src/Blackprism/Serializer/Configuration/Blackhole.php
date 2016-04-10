<?php

declare(strict_types=1);

namespace Blackprism\Serializer\Configuration;

use Blackprism\Serializer\Configuration;

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
     * @return $this
     */
    public function attributeUseMethod(string $attribute, string $setter, string $getter)
    {
        return $this;
    }

    /**
     * @param string $attribute
     * @param string $class
     * @param string $setter
     * @param string $getter
     *
     * @return $this
     */
    public function attributeUseObject(string $attribute, string $class, string $setter, string $getter)
    {
        return $this;
    }

    /**
     * @param string $attribute
     * @param string $class
     * @param string $setter
     * @param string $getter
     *
     * @return $this
     */
    public function attributeUseCollectionObject(string $attribute, string $class, string $setter, string $getter)
    {
        return $this;
    }

    /**
     * @param string $attribute
     * @param callable $serialize
     * @param callable $deserialize
     *
     * @return $this
     */
    public function attributeUseHandler(string $attribute, callable $serialize, callable $deserialize)
    {
        return $this;
    }

    /**
     * @param Configuration $configuration
     *
     * @return $this
     */
    public function registerToConfiguration(Configuration $configuration)
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
