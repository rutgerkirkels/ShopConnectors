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
    protected $marketpalceId;

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
     * @param string $marchantId
     * @return AmazonCredentials
     */
    public function setMaerchantId(string $merchantId): self
    {
        $this->merchantId = $merchantId;
        return $this;
    }

    /**
     * @return string
     */
    public function getMarketpalceId(): string
    {
        return $this->marketpalceId;
    }

    /**
     * @param string $marketpalceId
     * @return AmazonCredentials
     */
    public function setMarketpalceId(string $marketpalceId): self
    {
        $this->marketpalceId = $marketpalceId;
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