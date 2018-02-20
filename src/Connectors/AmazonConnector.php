<?php

namespace rutgerkirkels\ShopConnectors\Connectors;


use rutgerkirkels\ShopConnectors\Entities\Credentials\CredentialsInterface;
use rutgerkirkels\ShopConnectors\Models\DateRange;

class AmazonConnector extends AbstractConnector implements ConnectorInterface
{
    protected $webservice;

    public function __construct(string $host, CredentialsInterface $credentials)
    {
        parent::__construct($host, $credentials);


    }

    public function getOrdersByOrderDate(DateRange $dateRange)
    {
        // TODO: Implement getOrdersByOrderDate() method.
    }
}