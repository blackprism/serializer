<?php

declare(strict_types=1);

namespace Blackprism\Serializer\Configuration;

use Blackprism\Serializer\Configuration;
use Blackprism\Serializer\Configuration\Type\Blackhole;
use Blackprism\Serializer\Configuration\Type\HandlerDeserializer;
use Blackprism\Serializer\Configuration\Type\HandlerSerializer;
use Blackprism\Serializer\Value\ClassName;

/**
 * Object
 *
 * @property string $className
 * @property Type[string] $attributes
 * @property Blackhole $blackhole
 */
final class Object implements ObjectInterface
{

    /**
     * @var string
     */
    private $className;

    /**
     * @var Type[string]
     */
    private $attributes = [];

    /**
     * @var Blackhole
     */
    private $blackhole;

    /**
     * @param string $className
     */
    public function __construct(string $className)
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
     * @return Type
     */
    public function getTypeForAttribute(string $attribute): Type
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
