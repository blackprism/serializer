<?php

declare(strict_types=1);

namespace Blackprism\Serializer\Configuration;

use Blackprism\Serializer\Configuration;
use Blackprism\Serializer\Configuration\Type\Blackhole;
use Blackprism\Serializer\Configuration\Type\HandlerDeserializerInterface;
use Blackprism\Serializer\Configuration\Type\HandlerSerializerInterface;
use Blackprism\Serializer\Value\ClassName;

/**
 * Object
 *
 * @property ClassName $className
 * @property TypeInterface[string] $attributes
 * @property Blackhole $blackhole
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
     * @var Blackhole
     */
    private $blackhole;

    /**
     * @param ClassName $className
     */
    public function __construct(ClassName $className)
    {
        $this->className = $className;
        $this->blackhole = new Blackhole();
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
        $this->attributes[$attribute] = new Configuration\Type\Handler($deserialize, $serialize);

        return $this;
    }

    /**
     * @param Configuration $mapperConfiguration
     *
     * @return ObjectInterface
     */
    public function registerToConfiguration(Configuration $mapperConfiguration): ObjectInterface
    {
        $mapperConfiguration->addConfigurationObject($this->className, $this);

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
