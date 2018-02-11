<?php

namespace rutgerkirkels\ShopConnectors\Entities\Credentials;

/**
 * Class WoocommerceCredentials
 * @package rutgerkirkels\ShopConnectors\Entities\Credentials
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class WoocommerceCredentials implements CredentialsInterface
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $secret;

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
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     */
    public function setSecret(string $secret): self
    {
        $this->secret = $secret;
        return $this;
    }


}