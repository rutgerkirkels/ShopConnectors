<?php

namespace rutgerkirkels\ShopConnectors\Entities\Credentials;

/**
 * Class VisualsoftCredentials
 * @package rutgerkirkels\ShopConnectors\Entities\Credentials
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class VisualsoftCredentials implements CredentialsInterface
{
    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     * @return VisualsoftCredentials
     */
    public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return VisualsoftCredentials
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
     * @return VisualsoftCredentials
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }


}