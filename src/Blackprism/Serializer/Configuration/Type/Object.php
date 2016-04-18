<?php

declare(strict_types=1);

namespace Blackprism\Serializer\Configuration\Type;

use Blackprism\Serializer\Configuration\Type;
use Blackprism\Serializer\Value\ClassName;

/**
 * Object
 *
 * @property ClassName $className
 * @property string $setter
 * @property string $getter
 * @property bool $collection
 */
final class Object implements Type
{
    /**
     * @var ClassName
     */
    private $className;

    /**
     * @var string
     */
    private $setter;

    /**
     * @var string
     */
    private $getter;

    /**
     * @var bool
     */
    private $collection;

    /**
     * @param ClassName $className
     * @param string $setter
     * @param string $getter
     * @param bool $collection
     */
    public function __construct(ClassName $className, string $setter, string $getter, bool $collection = false)
    {
        $this->className = $className;
        $this->setter = $setter;
        $this->getter = $getter;
        $this->collection = $collection;
    }

    /**
     * @return ClassName
     */
    public function className(): ClassName
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function setter(): string
    {
        return $this->setter;
    }

    /**
     * @return string
     */
    public function getter(): string
    {
        return $this->getter;
    }

    /**
     * @return bool
     */
    public function isCollection(): bool
    {
        return $this->collection;
    }
}
