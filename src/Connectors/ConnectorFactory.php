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
     * @param string $shopType
     * @param string $host
     * @param CredentialsInterface $credentials
     * @return AbstractConnector
     * @throws \Exception
     */
    public static function build(string $shopType, string $host, CredentialsInterface $credentials)
    {
        $class = __NAMESPACE__ . '\\' . $shopType . 'Connector';
        if (class_exists($class)) {
            $connectorClass = new $class($host, $credentials);

            return $connectorClass;
        }
        else {
            throw new \Exception($shopType . ' is an invalid shopType');
        }
    }
}