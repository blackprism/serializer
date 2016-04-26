<?php

declare(strict_types=1);

namespace tests\fixtures;

/**
 * Country
 *
 * @property string $name
 */
class Country
{
    /**
     * @var string
     */
    private $name = '';

    /**
     * @var City[]
     */
    private $cities = [];

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param City[] $cities
     */
    public function citiesAre(array $cities)
    {
        $this->cities = $cities;
    }

    /**
     * @param City[] $cities
     */
    public function citiesInTraversable(\Traversable $cities)
    {
        $this->cities = $cities;
    }

    /**
     * @return array
     */
    public function getCities(): array
    {
        return $this->cities;
    }
}
