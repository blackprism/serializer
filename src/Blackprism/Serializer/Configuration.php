<?php

declare(strict_types=1);

namespace Blackprism\Serializer;

use Blackprism\Serializer\Configuration\Blackhole;
use Blackprism\Serializer\Configuration\ObjectInterface;
use Blackprism\Serializer\Value\ClassName;

/**
 * Configuration
 *
 * @property ObjectInterface[] $objects
 * @property string $identifierAttribute
 * @property mixed[] $identifiers
 */
final class Configuration
{
    /**
     * @var ObjectInterface[]
     */
    private $objects;

    /**
     * @var string|null
     */
    private $identifierAttribute;

    /**
     * @var mixed[]
     */
    private $identifiers = [];

    /**
     * @param string $attribute
     *
     * @return Configuration
     */
    public function identifierAttribute(string $attribute): self
    {
        $this->identifierAttribute = $attribute;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIdentifierAttribute()
    {
        return $this->identifierAttribute;
    }

    /**
     * @param ClassName $className
     * @param ObjectInterface $configurationObject
     *
     * @return Configuration
     */
    public function addConfigurationObject(ClassName $className, ObjectInterface $configurationObject): self
    {
        $this->objects[$className->getIdentifier()] = $configurationObject;

        return $this;
    }

    /**
     * @param ClassName $className
     * @param ObjectInterface $configurationObject
     * @param string $identifier
     *
     * @return Configuration
     */
    public function addConfigurationObjectWithIdentifier(
        ClassName $className,
        ObjectInterface $configurationObject,
        $identifier
    ): self {
        $this->identifiers[$identifier] = $configurationObject;
        $this->objects[$className->getIdentifier()] = $configurationObject;

        return $this;
    }

    /**
     * @param ClassName $className
     *
     * @return ObjectInterface
     */
    public function getConfigurationObjectForClass(ClassName $className): ObjectInterface
    {
        if (isset($this->objects[$className->getIdentifier()]) === true) {
            return $this->objects[$className->getIdentifier()];
        }

        return new Blackhole();
    }

    /**
     * @param string $identifier
     *
     * @return ObjectInterface
     */
    public function getConfigurationObjectForIdentifier(string $identifier): ObjectInterface
    {
        if (isset($this->identifiers[$identifier]) === true) {
            return $this->identifiers[$identifier];
        }

        return new Blackhole();
    }
}
