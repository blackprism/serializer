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
 * @property string $identifier
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
     * @var string
     */
    private $identifier = '';

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
     * @return ClassName
     */
    public function getClassName(): ClassName
    {
        return $this->className;
    }

    /**
     * @param string $identifier
     *
     * @return ObjectInterface
     */
    private function identifier(string $identifier): ObjectInterface
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
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
        $this->attributes[$attribute] = new Type\Method($setter, $getter);

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
        $this->attributes[$attribute] = new Type\Object($className, $setter, $getter);

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
        $this->attributes[$attribute] = new Type\Collection\Object($className, $setter, $getter);

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
        $this->attributes[$attribute] = new Type\IdentifiedObject($setter, $getter);

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
        $this->attributes[$attribute] = new Type\Collection\IdentifiedObject($setter, $getter);

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
        $this->attributes[$attribute] = new Type\Handler($deserialize, $serialize);

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
     * @param Configuration $configuration
     * @param string $identifier
     *
     * @return ObjectInterface
     */
    public function registerToConfigurationWithIdentifier(
        Configuration $configuration,
        string $identifier
    ): ObjectInterface {
        $this->identifier($identifier);
        $configuration->addConfigurationObjectWithIdentifier($this->className, $this, $identifier);

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
