<?php

declare(strict_types=1);

namespace tests\fixtures;

/**
 * City
 *
 * @property string $name
 * @property Country|null $country
 */
class City
{
    /**
     * @var string
     */
    private $name = '';

    /**
     * @var Country|null
     */
    private $country = null;

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
     * @param Country $country
     */
    public function countryIs($country)
    {
        $this->country = $country;
    }

    /**
     * @return Country|null
     */
    public function getCountry()
    {
        return $this->country;
    }
}
