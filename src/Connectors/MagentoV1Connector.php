<?php

namespace rutgerkirkels\ShopConnectors\Connectors;

use rutgerkirkels\ShopConnectors\Entities\Credentials\MagentoV1Credentials;
use rutgerkirkels\ShopConnectors\Models\Customer;
use rutgerkirkels\ShopConnectors\Models\DateRange;
use rutgerkirkels\ShopConnectors\Models\Item;
use rutgerkirkels\ShopConnectors\Models\Order;
use rutgerkirkels\ShopConnectors\Models\InvoiceAddress;
use rutgerkirkels\ShopConnectors\Models\DeliveryAddress;
use rutgerkirkels\ShopConnectors\Models\OrderLine;
use rutgerkirkels\ShopConnectors\Models\Payment;

/**
 * Class MagentoV1Connector
 * @package rutgerkirkels\ShopConnectors\Connectors
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class MagentoV1Connector extends AbstractConnector implements ConnectorInterface
{
    /**
     * @var \SoapClient
     */
    protected $webservice;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * MagentoV1Connector constructor.
     * @param string $host
     * @param MagentoV1Credentials $credentials
     */
    public function __construct(string $host, MagentoV1Credentials $credentials)
    {
        parent::__construct($host, $credentials);
        $this->webservice = new \SoapClient($this->getHost() . '/index.php/api/v2_soap?wsdl');
        $this->getSessionId();
    }

    /**
     * @param DateRange|null $dateRange
     * @return array
     * @throws \Exception
     */
    public function getOrdersByOrderDate(DateRange $dateRange = null)
    {
        $filter = [
            'complex_filter' => [
                [
                    'key' => 'created_at',
                    'value' => [
                        'key' => 'from',
                        'value' => $dateRange->getStart()->format('Y-m-d')
                    ]
                ],
                [
                    'key' => 'created_at',
                    'value' => [
                        'key' => 'to',
                        'value' => $dateRange->getEnd()->format('Y-m-d')
                    ]
                ]
            ]
        ];

        $magentoOrders = $this->webservice->salesOrderList(
            $this->sessionId,
            $filter
        );

        $orders=[];
        foreach ($magentoOrders as $magentoOrderData) {
            $magentoOrder = $this->webservice->salesOrderInfo($this->getSessionId(), $magentoOrderData->increment_id);

            $order = new Order();
            $order->setPlatform($this->getPlatform());
            $order->setDate($this->getTimestamp($magentoOrder->created_at));
            $order->setLastUpdate($this->getTimestamp($magentoOrder->updated_at));
            $order->setCustomer($this->getMagentoCustomer($magentoOrder));
            $order->setInvoiceAddress($this->getAddress($magentoOrder->billing_address, InvoiceAddress::class));
            $order->setDeliveryAddress($this->getAddress($magentoOrder->shipping_address, DeliveryAddress::class));
            $order->setOrderLines($this->getOrderLines($magentoOrder->items));
            $order->setExternalData(($this->getExternalData($magentoOrder)));
            $order->setPayment($this->getPayment($magentoOrder));
            $orders[] = $order;
        }

        return $orders;
    }

    /**
     * @param \stdClass $magentoOrder
     * @return Customer
     */
    protected function getMagentoCustomer(\stdClass $magentoOrder)
    {
        $customer = new Customer();
        $customer->setFirstName($magentoOrder->customer_firstname);
        $customer->setLastName($magentoOrder->customer_lastname);
        $customer->setEmail($magentoOrder->customer_email);

        // If the the customer is registered, we're going to retrieve more data
        if (!boolval($magentoOrder->customer_is_guest)) {
            $externalData = new Customer\ExternalData();
            $externalData->setId(intval($magentoOrder->customer_id));
            $customer->setExternalData($externalData);
        }
        return $customer;
    }

    /**
     * @param \stdClass $magentoAddress
     * @param string $type
     * @return mixed
     */
    protected function getAddress(\stdClass $magentoAddress, string $type)
    {
        $address = new $type;
        $address->setAddress($magentoAddress->street);
        $address->setPostalCode($magentoAddress->postcode);
        $address->setCity($magentoAddress->city);
        $address->setCountryIso2($magentoAddress->country_id);

        if (property_exists($magentoAddress, 'telephone')) {
            $address->addPhone($magentoAddress->telephone);
        }
        return $address;
    }

    /**
     * @param array $magentoOrderLines
     * @return array
     */
    protected function getOrderLines(array $magentoOrderLines)
    {
        $orderlines = [];
        foreach ($magentoOrderLines as $magentoOrderLine) {
            $item = new Item();
            $item->setName($magentoOrderLine->name);
            $item->setSku($magentoOrderLine->sku);
            $item->setPriceWithTax(floatval($magentoOrderLine->price));
            $item->setWeight(floatval($magentoOrderLine->weight));

            $orderlines[] = new OrderLine($item, $magentoOrderLine->qty_ordered);
        }

        return $orderlines;
    }

    /**
     * @return string
     */
    protected function getSessionId() {
        if (is_null($this->sessionId)) {
            $this->sessionId = $this->webservice->login($this->getCredentials()->getUsername(), $this->getCredentials()->getPassword());
        }

        return $this->sessionId;
    }

    /**
     * @param \stdClass $magentoOrder
     * @return Order\ExternalData
     */
    protected function getExternalData(\stdClass $magentoOrder)
    {
        $externalData = new Order\ExternalData();
        $externalData->setOrderId(intval($magentoOrder->order_id));
        $externalData->setOrderCode($magentoOrder->increment_id);
        $externalData->setOrderIp($magentoOrder->remote_ip);
        $externalData->setJson(json_encode($magentoOrder));
        return $externalData;
    }

    /**
     * @param \stdClass $order
     * @return Payment
     * @throws \Exception
     */
    protected function getPayment(\stdClass $order)
    {
        // TODO Get payment type
        $payment = new Payment();

        if (floatval($order->grand_total) === floatval($order->total_paid)) {
            $payment->setStatus('paid');
        }
        elseif (floatval($order->grand_total) - floatval($order->total_paid) > 0) {
            $payment->setStatus('partially_paid');
        }
        elseif (floatval($order->grand_total) - floatval($order->total_paid) === floatval($order->grand_total)) {
            $payment->setStatus('not_paid');
        }

        return $payment;
    }
}