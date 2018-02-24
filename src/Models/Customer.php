<?php

namespace rutgerkirkels\ShopConnectors\Models;
use rutgerkirkels\ShopConnectors\Models\Customer\ExternalData;

/**
 * Class Customer
 * @package rutgerkirkels\ShopConnectors\Models
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class Customer extends AbstractModel
{
    /**
     * @var string
     */
    protected $gender;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $middleName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $fullName;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var array
     */
    protected $phoneNumbers;

    /**
     * @var \DateTime
     */
    protected $dob;

    /**
     * @var string
     */
    protected $companyName;

    /**
     * @var string;
     */
    protected $companyVatId;

    /**
     * @var ExternalData
     */
    protected $externalData;

    /**
     * @return string
     */
    public function getGender(): string
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     * @return Customer
     * @throws \Exception
     */
    public function setGender(string $gender): self
    {
        if (strtolower($gender) !== 'male' && strtolower($gender) !== 'female') {
            throw new \Exception('Gender can only be male or female');
        }
        $this->gender = $gender;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return Customer
     */
    public function setFirstName(string $firstName) : self
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getMiddleName(): string
    {
        return $this->middleName;
    }

    /**
     * @param string $middleName
     * @return Customer
     */
    public function setMiddleName(string $middleName) : self
    {
        $this->middleName = $middleName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return Customer
     */
    public function setLastName(string $lastName) : self
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        if (is_null($this->fullName)) {
            $fullName = $this->getFirstName();

            if (!is_null($this->getMiddleName())) {
                $fullName .= ' ' . $this->getMiddleName();
            }

            $fullName .= ' ' . $this->getLastName();

            return $fullName;
        }

        return $this->fullName;
    }

    /**
     * @param string $fullName
     * @return Customer
     */
    public function setFullName(string $fullName) : self
    {
        $this->fullName = $fullName;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Customer
     */
    public function setEmail(string $email) : self
    {
        $this->email = $email;
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
     * @param Phone $phone
     * @return Customer
     */
    public function addPhoneNumber(Phone $phone) : self
    {
        $this->phoneNumbers[] = $phone;
        return $this;
    }


    /**
     * @return \DateTime
     */
    public function getDob(): \DateTime
    {
        return $this->dob;
    }

    /**
     * @param \DateTime $dob
     * @return Customer
     */
    public function setDob(\DateTime $dob) : self
    {
        $this->dob = $dob;
        return $this;
    }

    /**
     * @return string
     */
    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    /**
     * @param string $companyName
     * @return Customer
     */
    public function setCompanyName(string $companyName) : self
    {
        $this->companyName = $companyName;
        return $this;
    }

    /**
     * @return string
     */
    public function getCompanyVatId(): string
    {
        return $this->companyVatId;
    }

    /**
     * @param string $companyVatId
     * @return Customer
     */
    public function setCompanyVatId(string $companyVatId) : self
    {
        $this->companyVatId = $companyVatId;
        return $this;
    }

    /**
     * @return ExternalData
     */
    public function getExternalData(): ExternalData
    {
        return $this->externalData;
    }

    /**
     * @param ExternalData $externalData
     * @return Customer
     */
    public function setExternalData(ExternalData $externalData) : self
    {
        $this->externalData = $externalData;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCompany()
    {
        if (!is_null($this->companyName) || !is_null($this->companyVatId)) {
            return true;
        }

        return false;
    }
}