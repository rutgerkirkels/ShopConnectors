<?php

namespace rutgerkirkels\ShopConnectors\Entities\Credentials;

/**
 * Class LightspeedCredentials
 * @package rutgerkirkels\ShopConnectors\Entities\Credentials
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class LightspeedCredentials implements CredentialsInterface
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
     * @return LightspeedCredentials
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
     * @return LightspeedCredentials
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }


}