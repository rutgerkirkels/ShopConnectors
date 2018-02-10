<?php

namespace rutgerkirkels\ShopConnectors\Connectors;

/**
 * Class PrestashopConnector
 * @package rutgerkirkels\ShopConnectors\Connectors
 *
 * @author Rutger Kirkels <rutger2kirkels.nl>
 */
class PrestashopConnector extends AbstractConnector implements ConnectorInterface
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }


}