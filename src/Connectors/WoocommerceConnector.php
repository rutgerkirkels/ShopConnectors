<?php

namespace rutgerkirkels\ShopConnectors\Connectors;


use Automattic\WooCommerce\Client;
use rutgerkirkels\ShopConnectors\Entities\Credentials\CredentialsInterface;
use rutgerkirkels\ShopConnectors\Models\Customer;
use rutgerkirkels\ShopConnectors\Models\DateRange;
use rutgerkirkels\ShopConnectors\Models\DeliveryAddress;
use rutgerkirkels\ShopConnectors\Models\InvoiceAddress;
use rutgerkirkels\ShopConnectors\Models\Item;
use rutgerkirkels\ShopConnectors\Models\Order;
use rutgerkirkels\ShopConnectors\Models\OrderLine;

/**
 * Class WoocommerceConnector
 * @package rutgerkirkels\ShopConnectors\Connectors
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
     * @param string|null $host
     * @param CredentialsInterface|null $credentials
     */
    public function __construct(string $host = null, CredentialsInterface $credentials = null)
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
     */
    public function getOrders(DateRange $dateRange)
    {
        if (!is_null($dateRange)) {
            if (!is_null($dateRange->getStart())) {
                $params['after'] = $dateRange->getStart()->format('Y-m-d');
            }

            if (!is_null($dateRange->getEnd())) {
                $params['before'] = $dateRange->getEnd()->format('Y-m-d');
            }
        }

        $response = $this->webservice->get('orders', $params);

        $orders = [];
        foreach ($response->orders as $wcOrder) {

            $order = new Order();
            $order->setDate(new \DateTime($wcOrder->created_at));
            $order->setCustomer($this->getCustomer($wcOrder->customer));
            $order->setInvoiceAddress($this->getAddress($wcOrder->billing_address, InvoiceAddress::class));
            $order->setDeliveryAddress($this->getAddress($wcOrder->shipping_address, DeliveryAddress::class));
            $order->setOrderLines($this->getOrderLines($wcOrder->line_items));
            $orders[] = $order;
        }
        var_dump($orders);
    }

    /**
     * @param \stdClass $wcCustomerData
     * @return Customer
     */
    protected function getCustomer(\stdClass $wcCustomerData)
    {
        $customer = new Customer();
        $customer->setFirstName($wcCustomerData->first_name);
        $customer->setLastName($wcCustomerData->last_name);
        $customer->setEmail($wcCustomerData->email);

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
}