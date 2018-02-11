<?php

namespace rutgerkirkels\ShopConnectors\Entities\Credentials;

/**
 * Class ShopifyCredentials
 * @package rutgerkirkels\ShopConnectors\Entities\Credentials
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class ShopifyCredentials implements CredentialsInterface
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $password;

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): self
    {
        $this->key = $key;
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
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }


}