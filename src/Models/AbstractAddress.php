<?php

namespace rutgerkirkels\ShopConnectors\Models;


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
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
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
     */
    public function setPostalCode(string $postalCode): void
    {
        $this->postalCode = $postalCode;
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
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
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
     */
    public function setCountryIso2(string $countryIso2): void
    {
        $this->countryIso2 = $countryIso2;
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
     */
    public function setCountry(string $country): void
    {
        $this->country = $country;
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
     */
    public function addPhone(string $phoneNumber, string $type = 'landline'): void
    {
        $phone = new Phone($phoneNumber, $type);

        $this->phoneNumbers[] = $phone;
    }


}