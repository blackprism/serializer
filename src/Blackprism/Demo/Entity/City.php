<?php

namespace Blackprism\Demo\Entity;

class City
{

    protected $id          = null;
    protected $name        = null;
    protected $country     = null;
    protected $countries   = [];

    public function setId($id)
    {
        $this->id = (int) $id;
    }

    public function getId()
    {
        return (int) $this->id;
    }

    public function setName($name)
    {
        $this->name = (string) $name;
    }

    public function getName()
    {
        return (string) $this->name;
    }

    public function setCountryCode($countryCode)
    {
        $this->countryCode = (string) $countryCode;
    }

    public function getCountryCode()
    {
        return (string) $this->countryCode;
    }

    public function setDistrict($district)
    {
        $this->district = (string) $district;
    }

    public function getDistrict()
    {
        return (string) $this->district;
    }

    public function setPopulation($population)
    {
        $this->population = (int) $population;
    }

    public function getPopulation()
    {
        return (int) $this->population;
    }

    public function setDt(\DateTime $dt = null)
    {
        $this->dt = $dt;
    }

    public function getDt()
    {
        if (is_object($this->dt) === true) {
            return clone $this->dt;
        }

        return $this->dt;
    }

    public function setTutu($value)
    {
        $this->tutu = $value;
    }

    public function setBroum($value)
    {
        $this->broum = $value;
    }

    public function countryIs(Country $country)
    {
        $this->country = $country;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function countriesAre(array $countries)
    {
        $this->countries = $countries;
    }

    public function getCountries()
    {
        return $this->countries;
    }
}
