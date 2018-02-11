<?php

namespace rutgerkirkels\ShopConnectors\Connectors;

use DTS\eBaySDK\Finding\Services\FindingService;
use rutgerkirkels\ShopConnectors\Entities\Credentials\CredentialsInterface;
use rutgerkirkels\ShopConnectors\Models\DateRange;

class EbayConnector extends AbstractConnector implements ConnectorInterface
{
    protected $webservice;

    public function __construct(string $host = null, CredentialsInterface $credentials = null)
    {
        parent::__construct($host, $credentials);

        $this->webservice = new FindingService([
            'apiVersion'  => '1.13.0',
            'globalId'    => 'EBAY-US',
            'credentials' => [
                'appId' => $this->credentials->getAppId(),
                'certId' => $this->credentials->getCertId(),
                'devId' => $this->credentials->getDevId()
            ]
        ]);
    }

    public function getOrders(DateRange $dateRange = null) {
        //TODO Make this work
    }
}