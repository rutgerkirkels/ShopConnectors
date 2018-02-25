<?php

namespace rutgerkirkels\ShopConnectors\Connectors;

use GuzzleHttp\Client;
use rutgerkirkels\ShopConnectors\Entities\Credentials\ShopifyCredentials;
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
 * Class ShopifyConnector
 * @package rutgerkirkels\ShopConnectors\Connectors
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class ShopifyConnector extends AbstractConnector implements ConnectorInterface
{
    /**
     * @var Client
     */
    protected $webservice;

    /**
     * ShopifyConnector constructor.
     * @param string $host
     * @param PrestashopCredentials $credentials
     */
    public function __construct(string $host, ShopifyCredentials $credentials)
    {
        parent::__construct($host, $credentials);
        $this->webservice = new Client([
            'base_uri' => $this->host . '/admin/',
            'auth' => [$this->credentials->getKey(),$this->credentials->getPassword()],
            'headers' => [
                'User-Agent' => $this->userAgent
            ]
        ]);
    }

    /**
     * @param DateRange|null $dateRange
     * @return array
     */
    public function getOrdersByOrderDate(DateRange $dateRange = null)
    {
        $query = [];

        if (!is_null($dateRange)) {
            if (!is_null($dateRange->getStart())) {
                $query['created_at_min'] = $dateRange->getStart()->format('Y-m-d');
            }

            if (!is_null($dateRange->getEnd())) {
                $query['created_at_max'] = $dateRange->getEnd()->format('Y-m-d');
            }
        }

        $response = $this->webservice->request('GET', 'orders.json', [
            'query' => $query
        ]);

        $sfOrders = (json_decode((string) $response->getBody()))->orders;

        $orders = [];
        foreach ($sfOrders as $sfOrder) {
            $order = new Order();
            $order->setPlatform($this->getPlatform());
            $order->setDate($this->getTimestamp($sfOrder->created_at));
            $order->setLastUpdate($this->getTimestamp($sfOrder->updated_at));
            $order->setCustomer($this->getCustomer($sfOrder));
            $order->setInvoiceAddress($this->getAddress($sfOrder->billing_address, InvoiceAddress::class));
            $order->setDeliveryAddress($this->getAddress($sfOrder->shipping_address, DeliveryAddress::class));
            $order->setOrderLines($this->getOrderLines($sfOrder->line_items));
            $order->setExternalData($this->getExternalData($sfOrder));
            $order->setPayment($this->getPayment($sfOrder));
            $orders[] = $order;
        }

        return $orders;
    }

    /**
     * @param \stdClass $sfOrder
     * @return Customer
     */
    protected function getCustomer(\stdClass $sfOrder)
    {
        $customer = new Customer();
        $customer->setFirstName($sfOrder->customer->first_name);
        $customer->setLastName($sfOrder->customer->last_name);
        if (is_null($sfOrder->customer->email)) {
            $customer->setEmail($sfOrder->email);
        }
        else {
            $customer->setEmail($sfOrder->customer->email);
        }

        if (!is_null($sfOrder->phone)) {
            $customer->addPhoneNumber(new Phone($sfOrder->phone));
        }

        $externalData = new Customer\ExternalData();
        $externalData->setId($sfOrder->customer->id);

        $customer->setExternalData($externalData);
        return $customer;
    }

    /**
     * @param \stdClass $addressData
     * @param string $type
     * @return mixed
     */
    protected function getAddress(\stdClass $addressData, string $type)
    {
        $address = new $type;
        $address->setAddress($addressData->address1);
        $address->setPostalCode($addressData->zip);
        $address->setCity($addressData->city);
        $address->setCountryIso2($addressData->country);
        if (!is_null($addressData->phone)) {
            $address->addPhone(new Phone($addressData->phone));
        }
        return $address;
    }

    /**
     * @param array $sfOrderLines
     * @return array
     */
    protected function getOrderLines(array $sfOrderLines)
    {
        $orderLines = [];
        foreach ($sfOrderLines as $sfOrderLine) {
            $item = new Item();
            $item->setName($sfOrderLine->name);
            $item->setWeight($sfOrderLine->grams);
            $item->setSku($sfOrderLine->sku);
            $item->setPriceWithTax($sfOrderLine->price);
            $orderLines[] = new OrderLine($item, floatval($sfOrderLine->quantity));
        }

        return $orderLines;
    }

    /**
     * @param \stdClass $sfOrder
     * @return Order\ExternalData
     */
    protected function getExternalData(\stdClass $sfOrder)
    {
        $externalData = new Order\ExternalData();
        $externalData->setOrderId($sfOrder->id);
        $externalData->setOrderCode(strval($sfOrder->number));
        $externalData->setOrderIp($sfOrder->client_details->browser_ip);
        $externalData->setJson(json_encode($sfOrder));
        return $externalData;
    }

    protected function getPayment(\stdClass $sfOrder)
    {
        $payment = new Payment();
        $payment->setStatus($sfOrder->financial_status);

        $payment->setType($sfOrder->payment_gateway_names[0]);

        return $payment;
    }
}