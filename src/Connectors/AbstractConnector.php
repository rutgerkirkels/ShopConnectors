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

    /**
     * @var string
     */
    protected $lastError;

    /**
     * @var string
     */
    protected $userAgent = 'PHP ShopConnectors by Rutger Kirkels';

    /**
     * AbstractConnector constructor.
     * @param string $host
     * @param CredentialsInterface $credentials
     */
    public function __construct(string $host = '', CredentialsInterface $credentials)
    {
        $this->platform = $this->getPlatform();
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

    protected function getPlatform()
    {
        $path = explode('\\', get_class($this));
        preg_match_all('/([a-zA-Z0-9]*)Connector/', array_pop($path), $matches, PREG_SET_ORDER, 0);

        return $matches[0][1];
    }
    /**
     * @return string
     */
    public function getLastError(): string
    {
        return $this->lastError;
    }

    /**
     * @param string $userAgent
     */
    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }


}