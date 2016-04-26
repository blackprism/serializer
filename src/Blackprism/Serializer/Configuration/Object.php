<?php

declare(strict_types=1);

namespace Blackprism\Serializer\Configuration;

use Blackprism\Serializer\Configuration;
use Blackprism\Serializer\Configuration\Type;
use Blackprism\Serializer\Value\ClassName;

/**
 * Object
 *
 * @property ClassName $className
 * @property TypeInterface[string] $attributes
 * @property Type\Blackhole $blackhole
 */
final class Object implements ObjectInterface
{

    /**
     * @var ClassName
     */
    private $className;

    /**
     * @var TypeInterface[string]
     */
    private $attributes = [];

    /**
     * @var Type\Blackhole
     */
    private $blackhole;

    /**
     * @param ClassName $className
     */
    public function __construct(ClassName $className)
    {
        $this->className = $className;
        $this->blackhole = new Type\Blackhole();
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
        $this->attributes[$attribute] = new Configuration\Type\Method($setter, $getter);

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
        $this->attributes[$attribute] = new Configuration\Type\Object($className, $setter, $getter);

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
        $this->attributes[$attribute] = new Configuration\Type\Object($className, $setter, $getter, true);

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
        $this->attributes[$attribute] = new Configuration\Type\Handler($deserialize, $serialize);

        return $this;
    }

    /**
     * @param Configuration $configuration
     *
     * @return ObjectInterface
     */
    public function registerToConfiguration(Configuration $configuration): ObjectInterface
    {
        $configuration->addConfigurationObject($this->className, $this);

        return $this;
    }

    /**
     * @param string $attribute
     *
     * @return TypeInterface
     */
    public function getTypeForAttribute(string $attribute): TypeInterface
    {
        if (isset($this->attributes[$attribute]) === false) {
            return $this->blackhole;
        }

        return $this->attributes[$attribute];
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return array_keys($this->attributes);
    }
}
