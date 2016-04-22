<?php

declare(strict_types=1);

namespace Blackprism\Serializer\Value;

/**
 * ClassName
 *
 * @property string $className
 */
class ClassName
{
    /**
     * @param string $className
     */
    public function __construct(string $className)
    {
        if (class_exists($className) === false) {
            throw new \InvalidArgumentException($className . ' not found');
        }

        $this->className = $className;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->className;
    }
}
