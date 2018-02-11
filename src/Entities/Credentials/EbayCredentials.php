<?php

namespace rutgerkirkels\ShopConnectors\Entities\Credentials;


class EbayCredentials implements CredentialsInterface
{
    /**
     * @var string
     */
    protected $appId;

    /**
     * @var string
     */
    protected $devId;

    /**
     * @var string
     */
    protected $certId;

    /**
     * @return string
     */
    public function getAppId(): string
    {
        return $this->appId;
    }

    /**
     * @param string $appId
     * @return EbayCredentials
     */
    public function setAppId(string $appId): self
    {
        $this->appId = $appId;
        return $this;
    }

    /**
     * @return string
     */
    public function getDevId(): string
    {
        return $this->devId;
    }

    /**
     * @param string $devId
     * @return EbayCredentials
     */
    public function setDevId(string $devId): self
    {
        $this->devId = $devId;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertId(): string
    {
        return $this->certId;
    }

    /**
     * @param string $certId
     * @return EbayCredentials
     */
    public function setCertId(string $certId): self
    {
        $this->certId = $certId;
        return $this;
    }


}