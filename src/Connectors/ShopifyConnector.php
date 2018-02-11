<?php

namespace rutgerkirkels\ShopConnectors\Connectors;


use GuzzleHttp\Client;
use rutgerkirkels\ShopConnectors\Entities\Credentials\CredentialsInterface;
use rutgerkirkels\ShopConnectors\Models\Customer;
use rutgerkirkels\ShopConnectors\Models\DateRange;
use rutgerkirkels\ShopConnectors\Models\DeliveryAddress;
use rutgerkirkels\ShopConnectors\Models\InvoiceAddress;
use rutgerkirkels\ShopConnectors\Models\Item;
use rutgerkirkels\ShopConnectors\Models\Order;
use rutgerkirkels\ShopConnectors\Models\OrderLine;

class ShopifyConnector extends AbstractConnector implements ConnectorInterface
{
    /**
     * @var Client
     */
    protected $webservice;

    public function __construct(string $host = null, CredentialsInterface $credentials = null)
    {
        parent::__construct($host, $credentials);
        $this->webservice = new Client([
            'base_uri' => $this->host . '/admin/',
            'auth' => [$this->credentials->getKey(),$this->credentials->getPassword()]
        ]);
    }

    public function getOrders(DateRange $dateRange = null)
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

        foreach ($sfOrders as $sfOrder) {
            $order = new Order();
            $order->setDate(new \DateTime($sfOrder->created_at));
            $order->setCustomer($this->getCustomer($sfOrder->customer));
            $order->setInvoiceAddress($this->getAddress($sfOrder->billing_address, InvoiceAddress::class));
            $order->setDeliveryAddress($this->getAddress($sfOrder->shipping_address, DeliveryAddress::class));
            $order->setOrderLines($this->getOrderLines($sfOrder->line_items));
            $orders[] = $order;
            return $orders;
        }
    }

    protected function getCustomer(\stdClass $customerData)
    {
        $customer = new Customer();
        $customer->setFirstName($customerData->first_name);
        $customer->setLastName($customerData->last_name);
        $customer->setEmail($customerData->email);
        if (!is_null($customerData->phone)) {
            $customer->addPhoneNumber($customerData->phone);
        }
        return $customer;
    }

    protected function getAddress(\stdClass $addressData, string $type)
    {
        $address = new $type;
        $address->setAddress($addressData->address1);
        $address->setPostalCode($addressData->zip);
        $address->setCity($addressData->city);
        $address->setCountryIso2($addressData->country);
        if (!is_null($addressData->phone)) {
            $address->addPhone($addressData->phone);
        }
        return $address;
    }

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
}