<?php

namespace rutgerkirkels\ShopConnectors\Entities\Credentials;

/**
 * Class MagentoV1Credentials
 * @package rutgerkirkels\ShopConnectors\Entities\Credentials
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class MagentoV1Credentials implements CredentialsInterface
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
     * @return MagentoV1Credentials
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
     * @return MagentoV1Credentials
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }


}