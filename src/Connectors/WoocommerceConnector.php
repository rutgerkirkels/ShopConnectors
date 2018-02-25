<?php

namespace rutgerkirkels\ShopConnectors\Connectors;

use Automattic\WooCommerce\Client;
use rutgerkirkels\ShopConnectors\Entities\Credentials\WoocommerceCredentials;
use rutgerkirkels\ShopConnectors\Models\Customer;
use rutgerkirkels\ShopConnectors\Models\DateRange;
use rutgerkirkels\ShopConnectors\Models\DeliveryAddress;
use rutgerkirkels\ShopConnectors\Models\InvoiceAddress;
use rutgerkirkels\ShopConnectors\Models\Item;
use rutgerkirkels\ShopConnectors\Models\Order;
use rutgerkirkels\ShopConnectors\Models\OrderLine;
use rutgerkirkels\ShopConnectors\Models\Payment;

/**
 * Class WoocommerceConnector
 * @package rutgerkirkels\ShopConnectors\Connectors
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class WoocommerceConnector extends AbstractConnector implements ConnectorInterface
{
    /**
     * @var Client
     */
    protected $webservice;

    /**
     * WoocommerceConnector constructor.
     * @param string $host
     * @param WoocommerceCredentials $credentials
     */
    public function __construct(string $host, WoocommerceCredentials $credentials)
    {
        parent::__construct($host, $credentials);
        $this->webservice = new Client(
            $host,
            $this->credentials->getKey(),
            $this->credentials->getSecret()
        );
    }

    /**
     * @param DateRange $dateRange
     * @return array
     * @throws \Exception
     */
    public function getOrdersByOrderDate(DateRange $dateRange)
    {
        if (!is_null($dateRange)) {
            if (!is_null($dateRange->getStart())) {
                $params['after'] = $dateRange->getStart()->format('Y-m-d');
            }

            if (!is_null($dateRange->getEnd())) {
                $params['before'] = $dateRange->getEnd()->format('Y-m-d');
            }
        }

        $wcOrders = $this->webservice->get('orders', $params)->orders;

        $orders = [];
        foreach ($wcOrders as $wcOrder) {
            $order = new Order();
            $order->setPlatform($this->getPlatform());
            $order->setDate($this->getTimestamp($wcOrder->created_at));
            $order->setLastUpdate($this->getTimestamp($wcOrder->updated_at));
            $order->setCustomer($this->getCustomer($wcOrder));
            $order->setInvoiceAddress($this->getAddress($wcOrder->billing_address, InvoiceAddress::class));
            $order->setDeliveryAddress($this->getAddress($wcOrder->shipping_address, DeliveryAddress::class));
            $order->setOrderLines($this->getOrderLines($wcOrder->line_items));
            $order->setExternalData($this->getExternalData($wcOrder));
            $order->setPayment($this->getPayment($wcOrder));
            $orders[] = $order;
        }

        return $orders;
    }

    /**
     * @param \stdClass $wcOrder
     * @return Customer
     */
    protected function getCustomer(\stdClass $wcOrder)
    {
        $customer = new Customer();
        $customer->setFirstName($wcOrder->customer->first_name);
        $customer->setLastName($wcOrder->customer->last_name);
        $customer->setEmail($wcOrder->customer->email);

        $externalData = new Customer\ExternalData();
        $externalData->setId($wcOrder->customer->id);
        $customer->setExternalData($externalData);
        return $customer;
    }

    /**
     * @param \stdClass $wcAddressData
     * @param string $type
     * @return mixed
     */
    protected function getAddress(\stdClass $wcAddressData, string $type)
    {
        $address = new $type;
        $address->setAddress($wcAddressData->address_1);
        $address->setPostalCode($wcAddressData->postcode);
        $address->setCity($wcAddressData->city);
        $address->setCountryIso2($wcAddressData->country);

        if (property_exists($wcAddressData, 'phone')) {
            $address->addPhone($wcAddressData->phone);
        }
        return $address;
    }

    /**
     * @param array $wcOrderLines
     * @return array
     */
    protected function getOrderLines(array $wcOrderLines)
    {
        $orderLines = [];
        foreach ($wcOrderLines as $wcOrderLine) {
            $item = new Item();
            $item->setName($wcOrderLine->name);
//            $item->setWeight();
            $item->setSku($wcOrderLine->sku);
            $item->setPriceWithTax($wcOrderLine->price);
            $orderLines[] = new OrderLine($item, floatval($wcOrderLine->quantity));
        }

        return $orderLines;
    }

    /**
     * @param \stdClass $wcOrder
     * @return Order\ExternalData
     */
    protected function getExternalData(\stdClass $wcOrder)
    {
        $externalData = new Order\ExternalData();
        $externalData->setOrderId($wcOrder->id);
        $externalData->setOrderCode($wcOrder->order_number);
        $externalData->setOrderIp($wcOrder->customer_ip);
        $externalData->setJson(json_encode($wcOrder));
        return $externalData;
    }

    /**
     * @param \stdClass $wcOrder
     * @return Payment
     * @throws \Exception
     */
    protected function getPayment(\stdClass $wcOrder)
    {
        $payment = new Payment();
        if ($wcOrder->payment_details->paid) {
            $payment->setStatus('paid');
        }
        else {
            $payment->setStatus('not_paid');
        }

        $payment->setType($wcOrder->payment_details->method_id);

        return $payment;
    }
}