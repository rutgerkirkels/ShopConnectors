<?php

namespace rutgerkirkels\ShopConnectors\Entities\Credentials;

/**
 * Class BolCredentials
 * @package rutgerkirkels\ShopConnectors\Entities\Credentials
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class BolCredentials implements CredentialsInterface
{
    /**
     * @var string
     */
    protected $publicKey;

    /**
     * @var string
     */
    protected $privateKey;

    /**
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * @param string $publicKey
     * @return BolCredentials
     */
    public function setPublicKey(string $publicKey): self
    {
        $this->publicKey = $publicKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    /**
     * @param string $privateKey
     * @return BolCredentials
     */
    public function setPrivateKey(string $privateKey): self
    {
        $this->privateKey = $privateKey;
        return $this;
    }


}