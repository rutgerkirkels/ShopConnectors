<?php

namespace rutgerkirkels\ShopConnectors\Connectors;

use rutgerkirkels\ShopConnectors\Entities\Credentials\VisualsoftCredentials;
use rutgerkirkels\ShopConnectors\Models\Customer;
use rutgerkirkels\ShopConnectors\Models\DateRange;
use rutgerkirkels\ShopConnectors\Models\DeliveryAddress;
use rutgerkirkels\ShopConnectors\Models\InvoiceAddress;
use rutgerkirkels\ShopConnectors\Models\Item;
use rutgerkirkels\ShopConnectors\Models\Order;
use rutgerkirkels\ShopConnectors\Models\OrderLine;

/**
 * Class VisualsoftConnector
 * @package rutgerkirkels\ShopConnectors\Connectors
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class VisualsoftConnector extends AbstractConnector implements ConnectorInterface
{

    /**
     * @var \SoapClient
     */
    protected $webservice;

    /**
     * VisualsoftConnector constructor.
     * @param string $host
     * @param VisualsoftCredentials $credentials
     */
    public function __construct(string $host, VisualsoftCredentials $credentials)
    {
        parent::__construct($host, $credentials);

        $this->webservice = new \SoapClient(
            $host . '/api/soap/wsdl/3',
            [
                'trace' => true,
                'exceptions' => true,
                'use' => SOAP_LITERAL
            ]
        );

        $headerBody = new \stdClass();
        $headerBody->ClientId = $this->getCredentials()->getClientId();
        $headerBody->Username = $this->getCredentials()->getUsername();
        $headerBody->Password = $this->getCredentials()->getPassword();

        $this->webservice->__setSoapHeaders(new \SoapHeader('/api/soap/service', 'VSAuth', $headerBody                                                                                                                                                                                                                                                                                                                                              ));
    }

    /**
     * @param DateRange $dateRange
     * @return array
     */
    public function getOrdersByOrderDate(DateRange $dateRange)
    {
        $response = $this->webservice->GetOrdersByDateRange($dateRange->getStart()->format('Y-m-d'), $dateRange->getEnd()->format('Y-m-d'));
        $orders = [];
        foreach ($response->Result->WEB_ORDERS->WEB_ORDER as $vsOrder) {
            $order = new Order();
            $order->setPlatform($this->getPlatform())
                ->setDate($this->getTimestamp($vsOrder->ORDER->ORDER_DATE))
                ->setExternalData($this->getExternalData($vsOrder))
                ->setInvoiceAddress($this->getAddress($vsOrder, InvoiceAddress::class))
                ->setDeliveryAddress($this->getAddress($vsOrder, DeliveryAddress::class))
                ->setCustomer($this->getCustomer($vsOrder))
                ->setOrderLines($this->getOrderlines($vsOrder));

            $orders[] = $order;
        }

        return $orders;
    }

    /**
     * @param $vsOrder
     * @return Customer
     */
    protected function getCustomer($vsOrder)
    {
        $customer = new Customer();
        $customer
            ->setEmail($vsOrder->CUSTOMER->BILLING_EMAIL)
            ->setFirstName($vsOrder->CUSTOMER->BILLING_FIRSTNAME)
            ->setLastName($vsOrder->CUSTOMER->BILLING_LASTNAME);

        return $customer;
    }

    /**
     * @param $vsOrder
     * @param string $type
     * @return mixed
     */
    protected function getAddress($vsOrder, string $type)
    {
        $address = new $type;
        switch ($type) {
            case InvoiceAddress::class:
                $addressString = trim($vsOrder->CUSTOMER->BILLING_ADDRESS1);
                $addressString .= $vsOrder->CUSTOMER->BILLING_ADDRESS2 != '' ? $vsOrder->CUSTOMER->BILLING_ADDRESS2 : '';
                $address
                    ->setAddress($addressString)
                    ->setPostalCode($vsOrder->CUSTOMER->BILLING_POSTCODE)
                    ->setCity($vsOrder->CUSTOMER->BILLING_TOWN)
                    ->setCountryIso2($vsOrder->CUSTOMER->BILLING_COUNTRY_CODE);
                break;

            case DeliveryAddress::class:
                $addressString = trim($vsOrder->CUSTOMER->DELIVERY_ADDRESS1);
                $addressString .= $vsOrder->CUSTOMER->DELIVERY_ADDRESS2 != '' ? $vsOrder->CUSTOMER->DELIVERY_ADDRESS2 : '';
                $address
                    ->setAddress($addressString)
                    ->setPostalCode($vsOrder->CUSTOMER->DELIVERY_POSTCODE)
                    ->setCity($vsOrder->CUSTOMER->DELIVERY_TOWN)
                    ->setCountryIso2($vsOrder->CUSTOMER->DELIVERY_COUNTRY_CODE);
                break;
        }

        return $address;
    }

    /**
     * @param $vsOrder
     * @return array
     */
    protected function getOrderlines($vsOrder)
    {
        $orderLines = [];

        if (is_array($vsOrder->PRODUCTS->PRODUCT)) {
            foreach ($vsOrder->PRODUCTS->PRODUCT as $orderRow) {
                $item = new Item();
                $item
                    ->setName($orderRow->TITLE)
                    ->setSku($orderRow->PRODUCT_REFERENCE)
                    ->setEan13(intval($orderRow->EAN))
                    ->setPriceWithTax(floatval($orderRow->PRICE_INC))
                    ->setPriceWithoutTax(floatval($orderRow->PRICE_EX))
                    ->setWeight(floatval($orderRow->WEIGHT));
                $orderLines[] = new OrderLine($item, $orderRow->QUANTITY);
            }
        }
        else {
                $orderRow = $vsOrder->PRODUCTS->PRODUCT;
                $item = new Item();
                $item
                    ->setName($orderRow->TITLE)
                    ->setSku($orderRow->PRODUCT_REFERENCE)
                    ->setEan13(intval($orderRow->EAN))
                    ->setPriceWithTax(floatval($orderRow->PRICE_INC))
                    ->setPriceWithoutTax(floatval($orderRow->PRICE_EX))
                    ->setWeight(floatval($orderRow->WEIGHT));
                $orderLines[] = new OrderLine($item, $orderRow->QUANTITY);
            }

        return $orderLines;
    }

    /**
     * @param $vsOrder
     * @return Order\ExternalData
     */
    protected function getExternalData($vsOrder)
    {
        $externalData = new Order\ExternalData();
        $externalData->setJson(json_encode($vsOrder));

        return $externalData;
    }
}