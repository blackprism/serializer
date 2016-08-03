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
     * @var string|null
     */
    private $zipCode = null;

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
     * @param string|null $zipCode
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
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
