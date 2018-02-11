<?php

namespace rutgerkirkels\ShopConnectors\Connectors;

use rutgerkirkels\ShopConnectors\Entities\Credentials\CredentialsInterface;

/**
 * Class ConnectorFactory
 * @package rutgerkirkels\ShopConnectors\Connectors
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class ConnectorFactory
{

    /**
     * @var AbstractConnector
     */
    protected $connectorClass;

    /**
     * @param string $shopType
     * @param string $host
     * @param CredentialsInterface $credentials
     * @return AbstractConnector
     * @throws \Exception
     */
    public function build(string $shopType, string $host, CredentialsInterface $credentials)
    {
        $class = __NAMESPACE__ . '\\' . $shopType . 'Connector';
        if (class_exists($class)) {
            $this->connectorClass = new $class($host, $credentials);

            return $this->connectorClass;
        }
        else {
            throw new \Exception($shopType . ' is an invalid shopType');
        }
    }
}