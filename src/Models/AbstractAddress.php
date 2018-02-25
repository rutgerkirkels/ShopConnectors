<?php

namespace rutgerkirkels\ShopConnectors\Models;

/**
 * Class AbstractAddress
 * @package rutgerkirkels\ShopConnectors\Models
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class AbstractAddress extends AbstractModel
{
    /**
     * @var string
     */
    protected $address;

    /**
     * @var string
     */
    protected $postalCode;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var string
     */
    protected $countryIso2;

    /**
     * @var array
     */
    protected $phoneNumbers = [];

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     * @return AbstractAddress
     */
    public function setAddress(string $address) : self
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return string
     */
    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     * @return AbstractAddress
     */
    public function setPostalCode(string $postalCode) : self
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     * @return AbstractAddress
     */
    public function setCity(string $city) : self
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountryIso2(): string
    {
        return $this->countryIso2;
    }

    /**
     * @param string $countryIso2
     * @return AbstractAddress
     */
    public function setCountryIso2(string $countryIso2) : self
    {
        $this->countryIso2 = $countryIso2;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $country
     * @return AbstractAddress
     */
    public function setCountry(string $country) : self
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return array
     */
    public function getPhoneNumbers(): array
    {
        return $this->phoneNumbers;
    }

    /**
     * @param string $phoneNumber
     * @param string $type
     * @return AbstractAddress
     */
    public function addPhone(string $phoneNumber, string $type = 'landline') : self
    {
        $phone = new Phone($phoneNumber, $type);
        $this->phoneNumbers[] = $phone;

        return $this;
    }


}