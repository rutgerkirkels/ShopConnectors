<?php

namespace rutgerkirkels\ShopConnectors\Models;

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
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
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
     */
    public function setMiddleName(string $middleName): void
    {
        $this->middleName = $middleName;
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
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
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
     */
    public function setFullName(string $fullName): void
    {
        $this->fullName = $fullName;
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
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
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
     */
    public function addPhoneNumber(Phone $phone): void
    {
        $this->phoneNumbers[] = $phone;
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
     */
    public function setDob(\DateTime $dob): void
    {
        $this->dob = $dob;
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
     */
    public function setCompanyName(string $companyName): void
    {
        $this->companyName = $companyName;
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
     */
    public function setCompanyVatId(string $companyVatId): void
    {
        $this->companyVatId = $companyVatId;
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