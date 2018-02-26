<?php

namespace rutgerkirkels\ShopConnectors\Connectors;

use GuzzleHttp\Client;
use rutgerkirkels\ShopConnectors\Entities\Credentials\BolCredentials;
use rutgerkirkels\ShopConnectors\Entities\Credentials\CredentialsInterface;
use rutgerkirkels\ShopConnectors\Helpers\DateTime;
use rutgerkirkels\ShopConnectors\Models\Customer;
use rutgerkirkels\ShopConnectors\Models\DateRange;
use rutgerkirkels\ShopConnectors\Models\DeliveryAddress;
use rutgerkirkels\ShopConnectors\Models\InvoiceAddress;
use rutgerkirkels\ShopConnectors\Models\Item;
use rutgerkirkels\ShopConnectors\Models\Order;
use rutgerkirkels\ShopConnectors\Models\OrderLine;

/**
 * Class BolConnector
 * @package rutgerkirkels\ShopConnectors\Connectors
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class BolConnector extends AbstractConnector implements ConnectorInterface
{
    /**
     * @var Client
     */
    protected $webservice;

    /**
     * BolConnector constructor.
     * @param string $host
     * @param BolCredentials $credentials
     */
    public function __construct(string $host, BolCredentials $credentials)
    {
        parent::__construct($host, $credentials);
        $this->webservice = new Client([
            'base_uri' => $host,
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
        $headers = $this->getBolHeaders('GET', '/services/rest/orders/v2');

        $response = $this->webservice->request('GET', 'services/rest/orders/v2', [
            'headers' => $headers,
            'query' => [
                'fulfilment-method' => 'FBB'
            ]
        ]);

        $response = simplexml_load_string((string) $response->getBody());

        $orders = [];
        foreach ($response->Order as $bolOrder) {
            $orderTimestamp = $this->getTimestamp($bolOrder->DateTimeCustomer);
            if ($orderTimestamp >= $dateRange->getStart() && $orderTimestamp <= $dateRange->getEnd()) {
                $order = new Order();
                $order->setPlatform($this->getPlatform());
                $order->setDate($this->getTimestamp($bolOrder->DateTimeCustomer));
                $order->setCustomer($this->getCustomer($bolOrder));
                $order->setInvoiceAddress($this->getAddress($bolOrder->CustomerDetails->BillingDetails, InvoiceAddress::class));
                $order->setDeliveryAddress($this->getAddress($bolOrder->CustomerDetails->ShipmentDetails, DeliveryAddress::class));
                $order->setOrderLines($this->getOrderlines($bolOrder->OrderItems->OrderItem));
                $order->setExternalData($this->getExternalData($bolOrder));
                $orders[] = $order;
            }

        }

        return $orders;
    }

    /**
     * @param \SimpleXMLElement $bolOrder
     * @return Customer
     * @throws \Exception
     */
    protected function getCustomer(\SimpleXMLElement $bolOrder)
    {
        $customer = new Customer();
        switch ($bolOrder->CustomerDetails->BillingDetails->SalutationCode) {
            case '01':
                $customer->setGender('male');
                break;

            case '02':
                $customer->setGender('female');
                break;
        }
        $customer->setFirstName($bolOrder->CustomerDetails->BillingDetails->Firstname);
        $customer->setLastName($bolOrder->CustomerDetails->BillingDetails->Surname);
        $customer->setEmail($bolOrder->CustomerDetails->BillingDetails->Email);

        return $customer;
    }

    /**
     * @param \SimpleXMLElement $data
     * @param string $type
     * @return mixed
     */
    protected function getAddress(\SimpleXMLElement $data, string $type)
    {
        $address = new $type;
        $address->setAddress($data->Streetname . ' ' . $data->Housenumber);
        $address->setPostalCode($data->ZipCode);
        $address->setCity($data->City);
        $address->setCountryIso2($data->CountryCode);

        return $address;
    }

    /**
     * @param \SimpleXMLElement $bolOrderlines
     * @return array
     */
    protected function getOrderlines(\SimpleXMLElement $bolOrderlines)
    {
        $orderlines = [];
        foreach ($bolOrderlines as $bolOrderline) {
            $item = new Item();
            $item->setName($bolOrderline->Title);
            $item->setPriceWithTax(floatval($bolOrderline->OfferPrice));
            $item->setEan13(intval($bolOrderline->EAN));
            $item->setSku($bolOrderline->OfferReference);

            $orderlines[] = new OrderLine($item, floatval($bolOrderline->Quantity));
        }

        return $orderlines;
    }

    /**
     * @param \SimpleXMLElement $bolOrder
     * @return Order\ExternalData
     */
    protected function getExternalData(\SimpleXMLElement $bolOrder)
    {
        $externalData = new Order\ExternalData();
        $externalData->setOrderId(intval($bolOrder->OrderId));
        $externalData->setJson(json_encode($bolOrder));

        return $externalData;
    }

    /**
     * @param string $method
     * @param string $uri
     * @return mixed
     */
    protected function getBolHeaders(string $method, string $uri)
    {
        $signature = strtoupper($method) . PHP_EOL . PHP_EOL;
        $signature .= 'application/xml' . PHP_EOL;
        $signature .= gmdate('D, d M Y H:i:s T') . PHP_EOL;
        $signature .= 'x-bol-date:' . gmdate('D, d M Y H:i:s T') . PHP_EOL;
        $signature .= $uri;

        $headers['X-BOL-Authorization'] = $this->credentials->getPublicKey() . ':' . base64_encode(hash_hmac('SHA256', $signature, $this->credentials->getPrivateKey(), true));
        $headers['X-BOL-Date'] = gmdate('D, d M Y H:i:s T');
        $headers['Content-type'] = 'application/xml';
        $headers['Accept'] = 'application/vnd.orders-v2.1+xml';

        return $headers;
    }
}