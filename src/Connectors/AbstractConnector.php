<?php

namespace rutgerkirkels\ShopConnectors\Connectors;

use rutgerkirkels\ShopConnectors\Entities\Credentials\CredentialsInterface;
use rutgerkirkels\ShopConnectors\Helpers\DateTime;

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

    /**
     * @var string
     */
    protected $timezone;

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
     * @return string
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /**
     * @param string $timezone
     */
    public function setTimezone(string $timezone): void
    {
        $this->timezone = $timezone;
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

    /**
     * @param string $timestamp
     * @return \DateTime
     */
    protected function getTimestamp(string $timestamp) {
        if (DateTime::hasTimeZone($timestamp)) {
            return new \DateTime($timestamp);
        }
        else {
            if (is_null($this->timezone)) {
                return new \DateTime($timestamp, new \DateTimeZone(date_default_timezone_get()));
            }
            else {
                return new \DateTime($timestamp, new \DateTimeZone($this->timezone));
            }
        }
    }

}