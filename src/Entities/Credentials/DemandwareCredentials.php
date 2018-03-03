<?php

namespace rutgerkirkels\ShopConnectors\Entities\Credentials;

/**
 * Class DemandwareCredentials
 * @package rutgerkirkels\ShopConnectors\Entities\Credentials
 * @author
 */
class DemandwareCredentials implements CredentialsInterface
{
    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $apiPassword;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $shop;

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return DemandwareCredentials
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return DemandwareCredentials
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiPassword(): string
    {
        return $this->apiPassword;
    }

    /**
     * @param string $apiPassword
     * @return DemandwareCredentials
     */
    public function setApiPassword(string $apiPassword): self
    {
        $this->apiPassword = $apiPassword;
        return $this;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     * @return DemandwareCredentials
     */
    public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * @return string
     */
    public function getShop(): string
    {
        return $this->shop;
    }

    /**
     * @param string $shop
     * @return DemandwareCredentials
     */
    public function setShop(string $shop): self
    {
        $this->shop = $shop;
        return $this;
    }


}