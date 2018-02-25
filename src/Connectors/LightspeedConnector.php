<?php

namespace rutgerkirkels\ShopConnectors\Connectors;

use GuzzleHttp\Client;
use rutgerkirkels\ShopConnectors\Entities\Credentials\LightspeedCredentials;
use rutgerkirkels\ShopConnectors\Models\Customer;
use rutgerkirkels\ShopConnectors\Models\DateRange;
use rutgerkirkels\ShopConnectors\Models\DeliveryAddress;
use rutgerkirkels\ShopConnectors\Models\InvoiceAddress;
use rutgerkirkels\ShopConnectors\Models\Item;
use rutgerkirkels\ShopConnectors\Models\Order;
use rutgerkirkels\ShopConnectors\Models\OrderLine;
use rutgerkirkels\ShopConnectors\Models\Payment;
use rutgerkirkels\ShopConnectors\Models\Phone;

/**
 * Class LightspeedConnector
 * @package rutgerkirkels\ShopConnectors\Connectors
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class LightspeedConnector extends AbstractConnector implements ConnectorInterface
{
    const MAXRESULTSPERPAGE = 250;

    /**
     * @var Client
     */
    protected $webservice;

    /**
     * LightspeedConnector constructor.
     * @param string $host
     * @param LightspeedCredentials $credentials
     */
    public function __construct(string $host, LightspeedCredentials $credentials)
    {
        parent::__construct($host, $credentials);
        $this->webservice = new Client([
            'base_uri' => $this->host,
            'auth' => [$this->credentials->getKey(),$this->credentials->getPassword()],
            'headers' => [
                'User-Agent' => $this->userAgent
            ]
        ]);
    }

    /**
     * @param DateRange $dateRange
     * @return array
     * @throws \Exception
     */
    public function getOrdersByOrderDate(DateRange $dateRange)
    {
        $orders = [];
        $page = 1;
        $continue = true;

        while($continue) {

            $retrievedOrders = $this->getOrdersByOrderDateAndPage($dateRange, $page);
            $orders = array_merge($orders, $retrievedOrders);
            if (count($retrievedOrders) === self::MAXRESULTSPERPAGE) {
                $page += 1;
            }
            else {
                $continue = false;
            }
        }

        return $orders;
    }

    /**
     * @param DateRange|null $dateRange
     * @param int $page
     * @return array
     * @throws \Exception
     */
    protected function getOrdersByOrderDateAndPage(DateRange $dateRange = null, int $page = 1) {
        $query = [
            'status' => 'any',
            'page' => $page,
            'limit' => self::MAXRESULTSPERPAGE
        ];

        if (!is_null($dateRange)) {
            $query['created_at_min'] = $dateRange->getStart()->format('Y-m-d');
            $query['created_at_max'] = $dateRange->getEnd()->format('Y-m-d');
        }

        $response = $this->webservice->request('GET', 'orders.json', [
            'query' => $query
        ]);

        $lsOrders = (json_decode((string) $response->getBody()))->orders;

        foreach ($lsOrders as $lsOrder) {
            $order = new Order();
            $order->setPlatform($this->getPlatform());
            $order->setDate($this->getTimestamp($lsOrder->createdAt));
            $order->setLastUpdate($this->getTimestamp($lsOrder->updatedAt));
            $order->setCustomer($this->getCustomer($lsOrder));
            $order->setInvoiceAddress($this->getAddress($lsOrder, InvoiceAddress::class));
            $order->setDeliveryAddress($this->getAddress($lsOrder, DeliveryAddress::class));
            $order->setOrderLines($this->getOrderLines($lsOrder->id));
            $order->setExternalData(($this->getExternalData($lsOrder)));
            $order->setPayment($this->getPayment($lsOrder));
            $orders[] = $order;
        }

        return $orders;
    }

    /**
     * @param \stdClass $lsOrder
     * @return Customer
     */
    protected function getCustomer(\stdClass $lsOrder)
    {
        $customer = new Customer();
        $customer->setFirstName($lsOrder->firstname);
        $customer->setMiddleName($lsOrder->middlename);
        $customer->setLastName($lsOrder->lastname);
        $customer->setEmail($lsOrder->email);
        if (is_string($lsOrder->birthDate)) {
            $customer->setDob($lsOrder->birthDate);
        }

        if ($lsOrder->phone !== '') {
            $customer->addPhoneNumber(new Phone($lsOrder->phone, 'landline'));
        }

        if ($lsOrder->mobile !== '') {
            $customer->addPhoneNumber(new Phone($lsOrder->mobile, 'mobile'));
        }
        return $customer;
    }

    /**
     * @param \stdClass $lsOrder
     * @param string $type
     * @return mixed
     */
    protected function getAddress(\stdClass $lsOrder, string $type)
    {
        $address = new $type;

        switch ($type) {

            case InvoiceAddress::class:
                $address->setAddress($lsOrder->addressBillingStreet . ' ' . $lsOrder->addressBillingNumber);
                $address->setPostalCode($lsOrder->addressBillingZipcode);
                $address->setCity($lsOrder->addressBillingCity);
                $address->setCountryIso2(strtoupper($lsOrder->addressBillingCountry->code));
                break;

            case DeliveryAddress::class:
                $address->setAddress($lsOrder->addressShippingStreet . ' ' . $lsOrder->addressShippingNumber);
                $address->setPostalCode($lsOrder->addressShippingZipcode);
                $address->setCity($lsOrder->addressShippingCity);
                $address->setCountryIso2(strtoupper($lsOrder->addressShippingCountry->code));
                break;
        }

        return $address;
    }

    /**
     * @param int $orderId
     * @return array
     */
    protected function getOrderLines(int $orderId)
    {
        $response = $this->webservice->request('GET', 'orders/' . strval($orderId) . '/products.json');

        $lsOrderLines = (json_decode((string) $response->getBody()))->orderProducts;

        $orderLines = [];
        foreach ($lsOrderLines as$lsOrderLine) {
            $item = new Item();
            $itemName = $lsOrderLine->productTitle;

            if ($lsOrderLine->variantTitle !== '') {
                $itemName .= ' ' . $lsOrderLine->variantTitle;
            }
            $item->setName($itemName);
            $item->setSku($lsOrderLine->sku);
            $item->setEan13($lsOrderLine->ean);
            $item->setPriceWithTax($lsOrderLine->priceIncl);
            $item->setPriceWithoutTax($lsOrderLine->priceExcl);
            $item->setWeight($lsOrderLine->weight);
            $orderLines[] = new OrderLine($item, $lsOrderLine->quantityOrdered);
        }

        return $orderLines;
    }

    /**
     * @param \stdClass $lsOrder
     * @return Order\ExternalData
     */
    protected function getExternalData(\stdClass $lsOrder)
    {
        $externalData = new Order\ExternalData();
        $externalData->setOrderId($lsOrder->id);
        $externalData->setOrderCode($lsOrder->number);
        $externalData->setOrderIp($lsOrder->remoteIp);
        return $externalData;
    }

    /**
     * @param \stdClass $lsOrder
     * @return Payment
     * @throws \Exception
     */
    protected function getPayment(\stdClass $lsOrder)
    {
        $payment = new Payment();
        $payment->setStatus($lsOrder->paymentStatus);
        $payment->setType($lsOrder->paymentId);
        return $payment;
    }
}