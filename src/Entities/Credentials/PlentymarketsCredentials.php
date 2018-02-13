<?php

namespace rutgerkirkels\ShopConnectors\Entities\Credentials;


class PlentymarketsCredentials implements CredentialsInterface
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
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return PlentymarketsCredentials
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
     * @return PlentymarketsCredentials
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }


}