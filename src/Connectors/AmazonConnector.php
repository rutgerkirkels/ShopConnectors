<?php

namespace rutgerkirkels\ShopConnectors\Connectors;

use rutgerkirkels\ShopConnectors\Entities\Credentials\CredentialsInterface;
use rutgerkirkels\ShopConnectors\Models\AbstractAddress;
use rutgerkirkels\ShopConnectors\Models\Customer;
use rutgerkirkels\ShopConnectors\Models\DateRange;
use rutgerkirkels\ShopConnectors\Models\DeliveryAddress;
use rutgerkirkels\ShopConnectors\Models\InvoiceAddress;
use rutgerkirkels\ShopConnectors\Models\Item;
use rutgerkirkels\ShopConnectors\Models\Order;
use rutgerkirkels\ShopConnectors\Models\OrderLine;
use rutgerkirkels\ShopConnectors\Models\Phone;

/**
 * Class AmazonConnector
 * @package rutgerkirkels\ShopConnectors\Connectors
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class AmazonConnector extends AbstractConnector implements ConnectorInterface
{
    /**
     * @var \MarketplaceWebServiceOrders_Client
     */
    protected $webservice;

    /**
     * @var array
     */
    protected $regions = [
                            [
                            'name' => 'North America',
                            'serviceUrl' => 'https://mws.amazonservices.com',
                            'marketplaces' => [
                                    [
                                        'id' => 'A2EUQ1WTGCTBG2',
                                        'country' => 'Canada'
                                    ],
                                    [
                                        'id' => 'ATVPDKIKX0DER',
                                        'name' => 'United States'
                                    ],
                                    [
                                        'id' => 'A1AM78C64UM0Y8',
                                        'name' => 'Mexico'
                                    ],
                                ]
                            ],
                            [
                                'name' => 'Europe',
                                'serviceUrl' => 'https://mws-eu.amazonservices.com',
                                'marketplaces' => [
                                    [
                                    'id' => 'A1RKKUPIHCS9HS',
                                    'country' => 'Spain'
                                    ],
                                    [
                                        'id' => 'A1F83G8C2ARO7P',
                                        'name' => 'United Kingdom'
                                    ],
                                    [
                                        'id' => 'A13V1IB3VIYZZH',
                                        'name' => 'France'
                                    ],
                                    [
                                        'id' => 'A1PA6795UKMFR9',
                                        'name' => 'Germany'
                                    ],
                                    [
                                        'id' => 'APJ6JRA9NG5V4',
                                        'name' => 'Italy'
                                    ]
                                ],
                            ],
        [
            'name' => 'India',
            'serviceUrl' => 'https://mws.amazonservices.in',
            'marketplaces' => [
                'id' => 'A21TJRUUN4KGV',
                'country' => 'India'
            ]
        ],
        [
            'name' => 'China',
            'serviceUrl' => 'https://mws.amazonservices.com.cn',
            'marketplaces' => [
                'id' => 'AAHKV2X7AFYLW',
                'country' => 'China'
            ]
        ],
        [
            'name' => 'Japan',
            'serviceUrl' => 'https://mws.amazonservices.jp',
            'marketplaces' => [
                'id' => 'A1VC38T7YXB528',
                'country' => 'Japan'
            ]
        ],
        [
            'name' => 'Australia',
            'serviceUrl' => 'https://mws.amazonservices.com.au',
            'marketplaces' => [
                'id' => 'A39IBJ37TRP1C6',
                'country' => 'Australia'
            ]
        ]


    ];

    /**
     * AmazonConnector constructor.
     * @param string $host
     * @param CredentialsInterface $credentials
     * @throws \Exception
     */
    public function __construct(string $host, CredentialsInterface $credentials)
    {
        parent::__construct($host, $credentials);
        $this->webservice = new \MarketplaceWebServiceOrders_Client(
            $this->credentials->getKeyId(),
            $this->credentials->getSecretKey(),
            $this->userAgent,
            'v1',
            $this->getConfig()
        );
    }

    /**
     * @param DateRange $dateRange
     * @return array
     */
    public function getOrderIds(DateRange $dateRange)
    {
        $orders = $this->getOrdersByOrderDate($dateRange);

        $ids = [];
        if (count($orders) > 0) {
            foreach ($orders as $order) {
                $ids[] = $order->getExternalData()->getOrderCode();
            }
        }

        return $ids;
    }

    /**
     * @param DateRange $dateRange
     * @return array|bool
     */
    public function getOrdersByOrderDate(DateRange $dateRange)
    {
        $request = new \MarketplaceWebServiceOrders_Model_ListOrdersRequest();
        $request->setSellerId($this->credentials->getMerchantId());
        $request->setMarketplaceId($this->credentials->getMarketplaceId());

        $request->setCreatedAfter($dateRange->getStart()->format(DATE_ISO8601));
        $request->setCreatedBefore($dateRange->getEnd()->format(DATE_ISO8601));
        try {
            $response = $this->webservice->ListOrders($request);
        }
        catch (\MarketplaceWebServiceOrders_Exception $e) {
            return false;
        }

        $dom = new \DOMDocument();
        $dom->loadXML($response->toXML());
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $azOrders = simplexml_load_string($dom->saveXML());
        $orders = [];
        foreach ($azOrders->ListOrdersResult->Orders->Order as $azOrder) {
            $order = new Order();
            $order->setPlatform($this->getPlatform());
            $order->setDate($this->getTimestamp((string) $azOrder->PurchaseDate));
            $order->setLastUpdate($this->getTimestamp((string) $azOrder->LastUpdateDate));
            $order->setCustomer($this->getCustomer($azOrder));
            $order->setInvoiceAddress($this->getAddress($azOrder, InvoiceAddress::class));
            $order->setDeliveryAddress($this->getAddress($azOrder, DeliveryAddress::class));
//            $order->setOrderLines($this->getOrderLines($sfOrder->line_items));
            $order->setExternalData($this->getExternalData($azOrder));
//            $order->setPayment($this->getPayment($sfOrder));
            $orders[] = $order;
        }

        return $orders;
    }

    /**
     * @param string $orderCode
     * @return bool|Order
     */
    public function getOrderByOrderCode(string $orderCode)
    {
        $request = new \MarketplaceWebServiceOrders_Model_GetOrderRequest();
        $request->setSellerId($this->credentials->getMerchantId());
        $request->setAmazonOrderId($orderCode);

        try {
            $response = $this->webservice->getOrder($request);
        }
        catch (\MarketplaceWebServiceOrders_Exception $e) {
            return false;
        }

        $dom = new \DOMDocument();
        $dom->loadXML($response->toXML());
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $response= simplexml_load_string($dom->saveXML());

        $azOrder = $response->GetOrderResult->Orders->Order;

        $order = new Order();
        $order->setDate($this->getTimestamp((string) $azOrder->PurchaseDate));
        $order->setLastUpdate($this->getTimestamp((string) $azOrder->LastUpdateDate));
        $order->setCustomer($this->getCustomer($azOrder));
        $order->setInvoiceAddress($this->getAddress($azOrder, InvoiceAddress::class));
        $order->setDeliveryAddress($this->getAddress($azOrder, DeliveryAddress::class));
//            $order->setOrderLines($this->getOrderLines($sfOrder->line_items));
        $order->setExternalData($this->getExternalData($azOrder));
//            $order->setPayment($this->getPayment($sfOrder));

        return $order;
    }

    /**
     * @param string $orderId
     * @return array|bool
     */
    public function getOrderLinesByOrderId(string $orderId)
    {
        $request = new \MarketplaceWebServiceOrders_Model_ListOrderItemsRequest();
        $request->setSellerId($this->credentials->getMerchantId());
        $request->setAmazonOrderId($orderId);

        try {
            $response = $this->webservice->ListOrderItems($request);
        }
        catch (\MarketplaceWebServiceOrders_Exception $e) {
            return false;
        }

        $dom = new \DOMDocument();
        $dom->loadXML($response->toXML());
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $response= simplexml_load_string($dom->saveXML());

        $orderLines = [];
        foreach ($response->ListOrderItemsResult->OrderItems->OrderItem as $azOrderLine) {
            $item = new Item();
            $item->setSku($azOrderLine->SellerSKU);
            $item->setPriceWithoutTax(floatval($azOrderLine->ItemPrice->Amount));
            $item->setName($azOrderLine->Title);

            $orderLine = new OrderLine($item, floatval($azOrderLine->QuantityOrdered));

            $externalData = new OrderLine\ExternalData();
            $externalData->setJson(json_encode($azOrderLine));
            $orderLine->setExternalData($externalData);

            $orderLines[] = $orderLine;
        }

        return $orderLines;
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function getConfig() {
        return [
            'ServiceURL' => $this->getServiceUrl(),
            'ProxyHost' => null,
            'ProxyPort' => -1,
            'ProxyUsername' => null,
            'ProxyPassword' => null,
            'MaxErrorRetry' => 3,
        ];
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getServiceUrl() {
        foreach ($this->regions as $region) {
            foreach ($region['marketplaces'] as $marketplace) {
                if ($marketplace['id'] === $this->credentials->getMarketplaceId()) {
                    return $region['serviceUrl'] . '/Orders/2013-09-01';
                }
            }
        }
        throw new \Exception('Could match market place ID: ' . $this->credentials->getMarketplaceId());
    }

    /**
     * @param $azOrder
     * @return Customer
     */
    protected function getCustomer($azOrder) {
        $customer = new Customer();
        $customer->setFullName((string) $azOrder->BuyerName);
        $customer->setEmail((string) $azOrder->BuyerEmail);
        $customer->addPhoneNumber(new Phone((string) $azOrder->ShippingAddress->Phone));
        return $customer;
    }

    /**
     * @param $azOrder
     * @param $type
     * @return AbstractAddress
     */
    protected function getAddress($azOrder, $type)
    {
        $address = new $type;
        $addressString = '';
        $addressString .= isset($azOrder->ShippingAddress->AddressLine1) ? (string) $azOrder->ShippingAddress->AddressLine1 : '';
        $addressString .= isset($azOrder->ShippingAddress->AddressLine2) ? ' ' . (string) $azOrder->ShippingAddress->AddressLine2 : '';
        $address->setAddress($addressString);
        if (isset($azOrder->ShippingAddress->PostalCode)) {
            $address->setPostalCode($azOrder->ShippingAddress->PostalCode);
        }
        if (isset($azOrder->ShippingAddress->City)) {
            $address->setCity($azOrder->ShippingAddress->City);
        }

        if (isset($azOrder->ShippingAddress->Phone) && $azOrder->ShippingAddress->Phone !== '') {
            $address->addPhone($azOrder->ShippingAddress->Phone);
        }
        return $address;
    }

    /**
     * @param $azOrder
     * @return Order\ExternalData
     */
    protected function getExternalData($azOrder)
    {
        $externalData = new Order\ExternalData();
        $externalData->setOrderCode($azOrder->AmazonOrderId);
        $externalData->setJson(json_encode($azOrder));
        return $externalData;
    }
}