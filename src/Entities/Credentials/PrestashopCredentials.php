<?php

namespace rutgerkirkels\ShopConnectors\Entities\Credentials;

/**
 * Class PrestashopCredentials
 * @package rutgerkirkels\ShopConnectors\Entities\Credentials
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class PrestashopCredentials implements CredentialsInterface
{
    /**
     * string
     */
    protected $key;

    public function __construct(string $key = null)
    {
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


}