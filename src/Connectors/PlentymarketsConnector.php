<?php

namespace rutgerkirkels\ShopConnectors\Connectors;

use GuzzleHttp\Client;
use rutgerkirkels\ShopConnectors\Entities\Credentials\CredentialsInterface;
use rutgerkirkels\ShopConnectors\Models\AbstractAddress;
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
 * Class PlentymarketsConnector
 * @package rutgerkirkels\ShopConnectors\Connectors
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class PlentymarketsConnector extends AbstractConnector implements ConnectorInterface
{
    /**
     * @var Client
     */
    protected $webservice;

    /**
     * @var array
     */
    protected $countries = [];

    /**
     * @var \stdClass
     */
    protected $token;

    protected $paymentMethods;

    /**
     * PlentymarketsConnector constructor.
     * @param string $host
     * @param CredentialsInterface $credentials
     */
    public function __construct(string $host, CredentialsInterface $credentials)
    {
        parent::__construct($host, $credentials);

        $this->getCountryIds();

        $this->webservice = new Client([
            'base_uri' => $host . '/rest/',
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
        $this->getPaymentMethods();

        $accessToken = $this->getToken()->accessToken;

        $query = [
            'with[]' => 'addresses',
            'itemsPerPage' => 1000

        ];

        if (!is_null($dateRange->getStart())) {
            $query['createdAtFrom'] = $dateRange->getStart()->format($dateRange->getStart()::W3C);
        }

        if (!is_null($dateRange->getEnd())) {
            $query['createdAtTill'] = $dateRange->getEnd()->format($dateRange->getEnd()::W3C);
        }

        $response = $this->webservice->request('GET', 'orders', [
           'headers' => [
               'Authorization' => 'Bearer ' . $accessToken,
               'Accept' => 'application/json'
           ],
            'query' => $query
        ]);

        $pmOrders = (json_decode((string) $response->getBody()));

        $orders = [];
        foreach ($pmOrders->entries as $pmOrder) {
            $addresses = $this->processAdresses($pmOrder->addresses);

            $order = new Order();
            $order->setPlatform($this->getPlatform());
            $order->setDate($this->getTimestamp($pmOrder->createdAt));
            $order->setLastUpdate($this->getTimestamp($pmOrder->updatedAt));
            $order->setCustomer($this->getCustomer($pmOrder));

            foreach ($pmOrder->addressRelations as $addressRelation) {

                switch ($addressRelation->typeId) {

                    case 1:
                        $order->setInvoiceAddress($this->getAddress(
                            $addresses[$addressRelation->addressId],
                            InvoiceAddress::class
                            )
                        );
                        break;

                    case 2:
                        $order->setDeliveryAddress($this->getAddress(
                            $addresses[$addressRelation->addressId],
                            DeliveryAddress::class
                            )
                        );
                        break;
                }
            }

            $order->setOrderLines($this->getOrderLines($pmOrder->orderItems));
            $order->setPayment($this->getPayment($pmOrder));
            $order->setExternalData($this->getExternalData($pmOrder));
            $orders[] = $order;
        }

        return $orders;
    }

    /**
     * @param \stdClass $pmOrder
     * @return Customer
     */
    protected function getCustomer(\stdClass $pmOrder)
    {
        $customer = new Customer();
        $fullName = '';

        if ($pmOrder->addresses[0]->name1 !== '') {
            $fullName .= $pmOrder->addresses[0]->name1 . ' ';
        }

        if ($pmOrder->addresses[0]->name2 !== '') {
            $fullName .= $pmOrder->addresses[0]->name2 . ' ';
        }

        if ($pmOrder->addresses[0]->name3 !== '') {
            $fullName .= $pmOrder->addresses[0]->name3 . ' ';
        }

        if ($pmOrder->addresses[0]->name4 !== '') {
            $fullName .= $pmOrder->addresses[0]->name4 . ' ';
        }

        $customer->setFullName(trim($fullName));

        foreach ($pmOrder->addresses[0]->options as $option) {
            if ($option->typeId === 4) {
                $customer->addPhoneNumber(new Phone($option->value));
            }

            if ($option->typeId === 5) {
                $customer->setEmail($option->value);
            }
        }

        $externalData = new Customer\ExternalData();

        foreach ($pmOrder->relations as $relation) {
            if ($relation->relation === 'receiver') {
                $externalData->setId($relation->referenceId);
            }
        }

        $customer->setExternalData($externalData);
        return $customer;
    }

    /**
     * @param \stdClass $pmAddress
     * @param string $type
     * @return AbstractAddress
     */
    protected function getAddress(\stdClass $pmAddress, string $type)
    {
        $address = new $type;

        $addressString = '';

        if ($pmAddress->address1 !== '') {
            $addressString .= $pmAddress->address1 . ' ';
        }

        if ($pmAddress->address2 !== '') {
            $addressString .= $pmAddress->address2 . ' ';
        }

        if ($pmAddress->address3 !== '') {
            $addressString .= $pmAddress->address3 . ' ';
        }
        if ($pmAddress->address4 !== '') {
            $addressString .= $pmAddress->address4 . ' ';
        }

        $address->setAddress(trim($addressString));
        $address->setPostalCode($pmAddress->postalCode);
        $address->setCity($pmAddress->town);
        if (key_exists($pmAddress->countryId, $this->countries)) {
            $address->setCountryIso2($this->countries[$pmAddress->countryId]);
        }

        foreach ($pmAddress->options as $option) {
            if ($option->typeId === 4) {
                $address->addPhone($option->value);
            }
        }

        return $address;
    }

    /**
     * @param array $orderItems
     * @return array
     */
    protected function getOrderLines(array $orderItems) {
        $orderLines = [];
        foreach ($orderItems as $orderItem) {
            $item = new Item();
            $item->setName($orderItem->orderItemName);
            $item->setPriceWithTax($orderItem->amounts[0]->priceGross);
            $item->setPriceWithoutTax($orderItem->amounts[0]->priceNet);
            $item->setSku($orderItem->itemVariationId);
            $orderLines[] = new OrderLine($item, $orderItem->quantity);
        }

        return $orderLines;
    }

    /**
     * @param \stdClass $pmOrder
     * @return Payment
     * @throws \Exception
     */
    protected function getPayment(\stdClass $pmOrder) {

        $payment = new Payment();
        foreach ($pmOrder->properties as $property) {
            switch ($property->typeId) {

                case 3: // Plentymarkets Payment Method
                    if ($property->value > 0) {
                        $payment->setType($this->paymentMethods[$property->value]->name);
                    }

                    break;

                case 4: // Plentymarkets Payment Status
                    switch ($property->value) {

                        case 'paid':
                            $payment->setStatus('paid');
                            break;

                        case 'unpaid':
                            $payment->setStatus('not_paid');
                            break;

                        default:
                            $payment->setStatus('not_paid');
                    }
                    break;
            }
        }

        return $payment;
    }

    /**
     * @param \stdClass $pmOrder
     * @return Order\ExternalData
     */
    protected function getExternalData(\stdClass $pmOrder) {
        $externalData = new Order\ExternalData();
        $externalData->setOrderId($pmOrder->id);
        $externalData->setOrderCode($pmOrder->id);
        $externalData->setJson(json_encode($pmOrder));
        return $externalData;
    }

    /**
     * @return \stdClass
     */
    protected function getToken()
    {
        if (is_null($this->token)) {
            $response = $this->webservice->request('POST','login',[
                'json' => [
                    'username' => $this->credentials->getUsername(),
                    'password' => $this->credentials->getPassword()
                ]
            ]);
            $this->token = json_decode((string) $response->getBody());
        }

        return $this->token;
    }

    protected function getPaymentMethods() {
        $accessToken = $this->getToken()->accessToken;

        $response = $this->webservice->request('GET', 'payments/methods', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json'
            ]
        ]);

        $paymentMethods = json_decode((string) $response->getBody());

        foreach($paymentMethods as $paymentMethod) {
            $this->paymentMethods[$paymentMethod->id] = $paymentMethod;
        }
    }

    /**
     * @param array $pmAddresses
     * @return array
     */
    protected function processAdresses(array $pmAddresses)
    {
        $addresses = [];
        foreach ($pmAddresses as $pmAddress) {
            $addresses[$pmAddress->id] = $pmAddress;
        }

        return $addresses;
    }

    /**
     * Set's the PlentyMarket Country ID's
     */
    protected function getCountryIds() {
        $this->countries = [
            1 => 'DE',
            2 => 'AT',
            3 => 'BE',
            4 => 'CH',
            5 => 'CY',
            6 => 'CZ',
            7 => 'DK',
            8 => 'ES',
            9 => 'EE',
            10 => 'FR',
            11 => 'FI',
            12 => 'GB',
            13 => 'GR',
            14 => 'HU',
            15 => 'IT',
            16 => 'IE',
            17 => 'LU',
            18 => 'LV',
            19 => 'MT',
            20 => 'NO',
            21 => 'NL',
            22 => 'PT',
            23 => 'PL',
            24 => 'SE',
            25 => 'SG',
            26 => 'SK',
            27 => 'SI',
            28 => 'US'
        ];
        // TODO Add remaining countries
    }
}