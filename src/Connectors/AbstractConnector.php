<?php

namespace rutgerkirkels\ShopConnectors\Connectors;

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

    public function __construct()
    {

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


}