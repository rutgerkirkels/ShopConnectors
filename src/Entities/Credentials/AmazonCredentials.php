<?php

namespace rutgerkirkels\ShopConnectors\Entities\Credentials;

/**
 * Class AmazonCredentials
 * @package rutgerkirkels\ShopConnectors\Entities\Credentials
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class AmazonCredentials implements CredentialsInterface
{
    /**
     * @var string
     */
    protected $merchantId;

    /**
     * @var string
     */
    protected $marketplaceId;

    /**
     * @var string
     */
    protected $keyId;

    /**
     * @var string
     */
    protected $secretKey;

    /**
     * @var string
     */
    protected $serviceUrl;

    /**
     * @return string
     */
    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    /**
     * @param string $merchantId
     * @return AmazonCredentials
     */
    public function setMerchantId(string $merchantId): self
    {
        $this->merchantId = $merchantId;
        return $this;
    }

    /**
     * @return string
     */
    public function getMarketplaceId(): string
    {
        return $this->marketplaceId;
    }

    /**
     * @param string $marketplaceId
     * @return AmazonCredentials
     */
    public function setMarketplaceId(string $marketplaceId): self
    {
        $this->marketplaceId = $marketplaceId;
        return $this;
    }

    /**
     * @return string
     */
    public function getKeyId(): string
    {
        return $this->keyId;
    }

    /**
     * @param string $keyId
     * @return AmazonCredentials
     */
    public function setKeyId(string $keyId): self
    {
        $this->keyId = $keyId;
        return $this;
    }

    /**
     * @return string
     */
    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    /**
     * @param string $secretKey
     * @return AmazonCredentials
     */
    public function setSecretKey(string $secretKey): self
    {
        $this->secretKey = $secretKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getServiceUrl(): string
    {
        return $this->serviceUrl;
    }

    /**
     * @param string $serviceUrl
     * @return AmazonCredentials
     */
    public function setServiceUrl(string $serviceUrl): self
    {
        $this->serviceUrl = $serviceUrl;
        return $this;
    }


}