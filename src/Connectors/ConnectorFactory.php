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
     * @param CredentialsInterface $credentials
     * @return AbstractConnector
     */
    public function build(string $shopType, CredentialsInterface $credentials) {
        try {
            $class = __NAMESPACE__ . '\\' . $shopType . 'Connector';
            $this->connectorClass = new $class;
        }
        catch (\Exception $exception) {
            error_log($exception->getMessage(), E_ERROR);
        }
        $this->loadCredentials($credentials);
        return $this->connectorClass;
    }

    protected function loadCredentials(CredentialsInterface $credentials)
    {
        $this->connectorClass->setHost($credentials->getHost());
        $this->connectorClass->setKey($credentials->getKey());
    }

}