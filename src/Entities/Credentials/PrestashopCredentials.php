<?php

namespace rutgerkirkels\ShopConnectors\Entities\Credentials;

class PrestashopCredentials implements CredentialsInterface
{
    /**
     * string
     */
    protected $key;

    /**
     * @var string
     */
    protected $host;

    public function __construct(string $host = null, string $key = null)
    {
        $this->host = $host;
        $this->key = $key;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host)
    {
        $this->host = $host;
        return $this;
    }


}