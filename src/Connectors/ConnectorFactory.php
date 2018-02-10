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

    protected $host;

    protected $credentials;
    /**
     * @param string $shopType
     * @param CredentialsInterface $credentials
     * @return AbstractConnector
     */
    public function build(string $shopType, string $host, CredentialsInterface $credentials) {
        try {
            $class = __NAMESPACE__ . '\\' . $shopType . 'Connector';
            $this->connectorClass = new $class($host, $credentials);
        }
        catch (\Exception $exception) {
            error_log($exception->getMessage(), E_ERROR);
        }
        return $this->connectorClass;
    }


}