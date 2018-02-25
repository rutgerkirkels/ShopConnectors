<?php

namespace rutgerkirkels\ShopConnectors\Connectors;

use GuzzleHttp\Client;
use rutgerkirkels\ShopConnectors\Entities\Credentials\PrestashopCredentials;
use rutgerkirkels\ShopConnectors\Models\AbstractAddress;
use rutgerkirkels\ShopConnectors\Models\Customer;
use rutgerkirkels\ShopConnectors\Models\DateRange;
use rutgerkirkels\ShopConnectors\Models\DeliveryAddress;
use rutgerkirkels\ShopConnectors\Models\InvoiceAddress;
use rutgerkirkels\ShopConnectors\Models\Item;
use rutgerkirkels\ShopConnectors\Models\Order;
use rutgerkirkels\ShopConnectors\Models\OrderLine;
use rutgerkirkels\ShopConnectors\Models\Payment;

/**
 * Class PrestashopConnector
 * @package rutgerkirkels\ShopConnectors\Connectors
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class PrestashopConnector extends AbstractConnector implements ConnectorInterface
{

    /**
     * @var Client
     */
    protected $webservice;

    /**
     * @var array
     */
    protected $countryIsoCodes;

    /**
     * PrestashopConnector constructor.
     * @param string $host
     * @param PrestashopCredentials $credentials
     */
    public function __construct(string $host, PrestashopCredentials $credentials)
    {
        parent::__construct($host, $credentials);
        $this->webservice = new Client([
            'base_uri' => $this->host . '/api/',
            'auth' => [$this->credentials->getKey(),''],
            'headers' => [
                'User-Agent' => $this->userAgent
            ]
        ]);
    }

    /**
     * @param DateRange $dateRange
     * @return array
     */
    public function getOrdersByOrderDate(DateRange $dateRange = null)
    {
        $this->getCountryIsoCodes();

        $query = [
            'display' => 'full',
            'date' => '1',
            'output_format' => 'JSON'
        ];

        if (!is_null($dateRange)) {
            $query['filter[date_add]'] = '[' . $dateRange->getStart()->format('Y-m-d') . ',' . $dateRange->getEnd()->format('Y-m-d') . ']';
        }

        $response = $this->webservice->request('GET', 'orders', [
            'query' => $query
        ]);

        $psOrders = (json_decode((string) $response->getBody()))->orders;

        $orders = [];
        foreach ($psOrders as $psOrder) {
            $order = new Order();
            $order->setPlatform($this->getPlatform());
            $order->setDate($this->getTimestamp($psOrder->date_add));
            $order->setLastUpdate($this->getTimestamp($psOrder->date_upd));
            $order->setCustomer($this->getCustomer($psOrder->id_customer));
            $order->setInvoiceAddress($this->getAddress($psOrder->id_address_invoice, InvoiceAddress::class));
            $order->setDeliveryAddress($this->getAddress($psOrder->id_address_delivery, DeliveryAddress::class));
            $order->setOrderLines($this->getOrderLines($psOrder->id));
            $order->setExternalData(($this->getExternalData($psOrder)));
            $order->setPayment($this->getPayment($psOrder));
            $orders[] = $order;
        }

        return $orders;
    }

    /**
     * @param int $customerId
     * @return Customer
     */
    protected function getCustomer(int $customerId)
    {
        $query = [
            'output_format' => 'JSON'
        ];

        $response = $this->webservice->request('GET', 'customers/' . strval($customerId), [
            'query' => $query
        ]);

        $psCustomer = (json_decode((string) $response->getBody()))->customer;

        $customer = new Customer();
        $customer->setFirstName($psCustomer->firstname);
        $customer->setLastName($psCustomer->lastname);
        $customer->setEmail($psCustomer->email);

        return $customer;
    }

    /**
     * @param int $addressId
     * @param string $type
     * @return AbstractAddress
     */
    protected function getAddress(int $addressId, string $type)
    {
        $query = [
            'output_format' => 'JSON'
        ];

        $response = $this->webservice->request('GET', 'addresses/' . strval($addressId), [
            'query' => $query
        ]);

        $psAddress = (json_decode((string) $response->getBody()))->address;

        $address = new $type;
        $address->setAddress($psAddress->address1);
        $address->setPostalCode($psAddress->postcode);
        $address->setCity($psAddress->city);
        $address->setCountryIso2($this->countryIsoCodes[$psAddress->id_country]);

        if ($psAddress->phone != '') {
            $address->addPhone($psAddress->phone);
        }

        if ($psAddress->phone_mobile != '') {
            $address->addPhone($psAddress->phone_mobile, 'mobile');
        }
        return $address;
    }

    /**
     * @param int $orderId
     * @return array
     */
    protected function getOrderLines(int $orderId) {
        $query = [
            'resource' => 'order_details',
            'filter[id_order]' => '[' . $orderId . ']',
            'display' => 'full',
            'output_format' => 'JSON'
        ];

        $response = $this->webservice->request('GET', 'order_details', [
            'query' => $query
        ]);

        $psOrderDetails = (json_decode((string) $response->getBody()));
        $orderLines = [];
        foreach ($psOrderDetails->order_details as $psOrderDetail) {
            $item = new Item();
            $item->setName($psOrderDetail->product_name);
            $item->setSku($psOrderDetail->product_reference);
            if ($psOrderDetail->product_ean13 !== '') {
                $item->setEan13(intval($psOrderDetail->product_ean13));
            }
            if ($psOrderDetail->product_upc !== '') {
                $item->setUpc(intval($psOrderDetail->product_upc));
            }
            $item->setWeight(floatval($psOrderDetail->product_weight));
            $item->setPriceWithoutTax(floatval($psOrderDetail->unit_price_tax_excl));
            $orderLines[] = new OrderLine($item, (float) $psOrderDetail->product_quantity);
        }

        return $orderLines;
    }

    /**
     * Get's the country codes and their ID's from Prestahop
     */
    protected function getCountryIsoCodes() {
        $query = [
            'display' => 'full',
            'output_format' => 'JSON'
        ];

        $response = $this->webservice->request('GET', 'countries', [
            'query' => $query
        ]);

        $psCountries = (json_decode((string) $response->getBody()));
        foreach ($psCountries->countries as $psCountry) {
            $this->countryIsoCodes[$psCountry->id] = $psCountry->iso_code;
        }
    }

    /**
     * @param \stdClass $psOrder
     * @return Order\ExternalData
     */
    protected function getExternalData(\stdClass $psOrder)
    {
        $externalData = new Order\ExternalData();
        $externalData->setOrderId($psOrder->id);
        $externalData->setOrderCode($psOrder->reference);
        $externalData->setJson(json_encode($psOrder));
        return $externalData;
    }

    protected function getPayment(\stdClass $psOrder)
    {
        $payment = new Payment();

        $totalAmount = floatval($psOrder->total_products_wt) + floatval($psOrder->total_shipping_tax_incl);
        if (floatval($psOrder->total_paid_tax_incl) === $totalAmount) {
            $payment->setStatus('paid');
        }
        elseif ($totalAmount - floatval($psOrder->total_paid_tax_incl) > 0) {
            $payment->setStatus('partially_paid');
        }
        else {
            $payment->setStatus('not_paid');
        }
        $payment->setType($psOrder->payment);
        return $payment;
    }
}