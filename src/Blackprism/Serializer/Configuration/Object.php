<?php

declare(strict_types=1);

namespace Blackprism\Serializer\Configuration;

use Blackprism\Serializer\Configuration;
use Blackprism\Serializer\Configuration\Type\Blackhole;

/**
 * Object
 *
 * @property string $className
 * @property Blackhole $blackhole
 * @property Type[string] $attributes
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
     * @return $this
     */
    public function attributeUseMethod(string $attribute, string $setter, string $getter)
    {
        $this->attributes[$attribute] = new Configuration\Type\Method($setter, $getter);

        return $this;
    }

    /**
     * @param string $attribute
     * @param string $className
     * @param string $setter
     * @param string $getter
     *
     * @return $this
     */
    public function attributeUseObject(string $attribute, string $className, string $setter, string $getter)
    {
        $this->attributes[$attribute] = new Configuration\Type\Object($className, $setter, $getter);

        return $this;
    }

    /**
     * @param string $attribute
     * @param string $className
     * @param string $setter
     * @param string $getter
     *
     * @return $this
     */
    public function attributeUseCollectionObject(string $attribute, string $className, string $setter, string $getter)
    {
        $this->attributes[$attribute] = new Configuration\Type\Object($className, $setter, $getter, true);

        return $this;
    }

    /**
     * @param string   $attribute
     * @param callable $deserialize
     * @param callable $serialize
     *
     * @return $this
     */
    public function attributeUseHandler(string $attribute, callable $deserialize, callable $serialize)
    {
        $this->attributes[$attribute] = new Configuration\Type\Handler($deserialize, $serialize);

        return $this;
    }

    /**
     * @param Configuration $mapperConfiguration
     *
     * @return $this
     */
    public function registerToConfiguration(Configuration $mapperConfiguration)
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
