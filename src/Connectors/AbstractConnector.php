<?php

namespace rutgerkirkels\ShopConnectors\Connectors;
use rutgerkirkels\ShopConnectors\Entities\Credentials\CredentialsInterface;

/**
 * Class AbstractConnector
 * @package rutgerkirkels\ShopConnectors\Connectors
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class AbstractConnector
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var CredentialsInterface
     */
    protected $credentials;

    public function __construct(string $host = null, CredentialsInterface $credentials = null)
    {
        $this->host = $host;
        $this->credentials = $credentials;
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
    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    /**
     * @return CredentialsInterface
     */
    public function getCredentials(): CredentialsInterface
    {
        return $this->credentials;
    }

    /**
     * @param CredentialsInterface $credentials
     */
    public function setCredentials(CredentialsInterface $credentials): void
    {
        $this->credentials = $credentials;
    }



}