<?php

namespace rutgerkirkels\ShopConnectors\Connectors;

use GuzzleHttp\Client;
use rutgerkirkels\ShopConnectors\Entities\Credentials\DemandwareCredentials;
use rutgerkirkels\ShopConnectors\Models\Customer;
use rutgerkirkels\ShopConnectors\Models\DateRange;
use rutgerkirkels\ShopConnectors\Models\DeliveryAddress;
use rutgerkirkels\ShopConnectors\Models\InvoiceAddress;
use rutgerkirkels\ShopConnectors\Models\Item;
use rutgerkirkels\ShopConnectors\Models\Order;
use rutgerkirkels\ShopConnectors\Models\OrderLine;
use rutgerkirkels\ShopConnectors\Models\Payment;

/**
 * Class DemandwareConnector
 * @package rutgerkirkels\ShopConnectors\Connectors
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class DemandwareConnector extends AbstractConnector implements ConnectorInterface
{
    /**
     * @var Client
     */
    protected $webservice;

    /**
     * @var string
     */
    protected $token;

    /**
     * DemandwareConnector constructor.
     * @param string $host
     * @param DemandwareCredentials $credentials
     */
    public function __construct(string $host = '', DemandwareCredentials $credentials)
    {
        parent::__construct($host, $credentials);

        $this->webservice = new Client([
            'base_uri' => $this->host,
            'headers' => [
                'User-Agent' => $this->userAgent,
                'Authorization' => 'Bearer ' . $this->getToken()
            ]
        ]);
    }

    /**
     * @param DateRange $dateRange
     * @param int $start
     * @return array
     * @throws \Exception
     */
    public function getOrdersByOrderDate(DateRange $dateRange, int $start = 0)
    {
        $dwOrders = [];
        $nextPage = true;

        while($nextPage) {

            $data = [
                'query' => [
                    'filtered_query' => [
                        'query' => [
                            'term_query' => [
                                'fields' => [
                                    'creation_date'
                                ],
                                'operator' => 'is_not_null'
                            ]
                        ],
                        'filter' => [
                            'range_filter' => [
                                'field' => 'creation_date',
                                'from' => $dateRange->getStart()->format('Y-m-d\T00:00:00'),
                                'to' => $dateRange->getEnd()->format('Y-m-d\T00:00:00')
                            ]
                        ]

                    ],


                ],
                'select' => '(**)',
                'start' => $start,
                'count' => 100
            ];

            $response = $this->webservice->request('POST', 's/' .$this->getCredentials()->getShop() . '/dw/shop/v16_8/order_search', [
                'json' => $data,
                'headers' => [
                    'Content-type' => 'application/json; charset=utf-8'
                ]
            ]);

            $orderData = json_decode((string) $response->getBody());

            foreach ($orderData->hits as $dwOrder) {
                $dwOrders[] = $dwOrder;
            }

            if (count($dwOrders) == $orderData->total) {
                $nextPage = false;
            }
            else {
                $start+= 100;
            }
        }

        foreach ($dwOrders as $dwOrder) {
            $order = new Order();
            $order->setPlatform($this->getPlatform());
            $order->setDate($this->getTimestamp($dwOrder->data->creation_date));
            $order->setLastUpdate($this->getTimestamp($dwOrder->data->creation_date));
            $order->setCustomer($this->getCustomer($dwOrder->data));
            $order->setInvoiceAddress($this->getAddress($dwOrder->data, InvoiceAddress::class));
            $order->setDeliveryAddress($this->getAddress($dwOrder->data, DeliveryAddress::class));
            $order->setOrderLines($this->getOrderLines($dwOrder->data->product_items));
            $order->setExternalData($this->getExternalData($dwOrder->data));
            $order->setPayment(($this->getPayment($dwOrder->data)));

            $orders[] = $order;
        }

        return $orders;
    }

    /**
     * @param \stdClass $orderData
     * @return Customer
     */
    protected function getCustomer(\stdClass $orderData)
    {
        $customer = new Customer();
        $customer
            ->setFirstName($orderData->billing_address->first_name)
            ->setLastName($orderData->billing_address->last_name)
            ->setFullName($orderData->billing_address->full_name)
            ->setEmail($orderData->customer_info->email);

        return $customer;
    }

    /**
     * @param \stdClass $orderData
     * @param string $type
     * @return mixed
     */
    protected function getAddress(\stdClass $orderData, string $type)
    {
        $address = new $type;

        switch ($type) {

            case InvoiceAddress::class:
                $address
                    ->setAddress($orderData->billing_address->address1)
                    ->setPostalCode($orderData->billing_address->postal_code)
                    ->setCity($orderData->billing_address->city)
                    ->setCountryIso2($orderData->billing_address->country_code);

                if (isset ($orderData->billing_address->phone) && $orderData->billing_address->phone !== '') {
                    $address->addPhone($orderData->billing_address->phone);
                }
                break;

            case DeliveryAddress::class:
                $address
                    ->setAddress($orderData->billing_address->address1)
                    ->setPostalCode($orderData->billing_address->postal_code)
                    ->setCity($orderData->billing_address->city)
                    ->setCountryIso2($orderData->billing_address->country_code);

                if (isset ($orderData->billing_address->phone) && $orderData->billing_address->phone !== '') {
                    $address->addPhone($orderData->billing_address->phone);
                }
                break;
                break;
        }

        return $address;
    }

    /**
     * @param array $dwOrderLines
     * @return array
     */
    protected function getOrderLines(array $dwOrderLines)
    {
        $orderLines = [];
        foreach ($dwOrderLines as $dwOrderLine) {
            $item = new Item();
            $item
                ->setName($dwOrderLine->item_text)
                ->setSku($dwOrderLine->item_id)
                ->setPriceWithTax($dwOrderLine->price)
                ->setPriceWithoutTax($dwOrderLine->price - $dwOrderLine->tax);

            $orderLines[] = new OrderLine($item, $dwOrderLine->quantity);
        }

        return $orderLines;
    }

    /**
     * @param $dwOrderData
     * @return Order\ExternalData
     */
    protected function getExternalData($dwOrderData)
    {
        $externalData = new Order\ExternalData();
        $externalData->setOrderId($dwOrderData->order_no);
        $externalData->setOrderCode($dwOrderData->order_no);
        $externalData->setJson(json_encode($dwOrderData));

        return $externalData;
    }

    /**
     * @param $dwOrderData
     * @return Payment
     * @throws \Exception
     */
    protected function getPayment($dwOrderData)
    {
        $payment = new Payment();
        $payment->setStatus($dwOrderData->payment_status);

        return $payment;
    }

    /**
     * @return string
     */
    protected function getToken() {
        if (!is_null($this->token)) {

            if ($this->token->valid_until > new \DateTime()) {
                return $this->token->token;
            }
        }

        $authCode = base64_encode($this->getCredentials()->getUsername().':'.$this->getCredentials()->getPassword().':'.$this->getCredentials()->getApiPassword());
        $client = new Client([
            'base_uri' => $this->host
        ]);
        $response = $client->request('POST', 'dw/oauth2/access_token', [
            'query' => [
                'client_id' => $this->getCredentials()->getClientId()
            ],
            'form_params' => [
                'grant_type' => 'urn:demandware:params:oauth:grant-type:client-id:dwsid:dwsecuretoken'
            ],
            'headers' => [
                'Authorization' => 'Basic ' . $authCode
            ]
        ]);

        $tokenData = json_decode( (string) $response->getBody());
        $this->token = new \stdClass();
        $this->token->valid_until = new \DateTime('+' . $tokenData->expires_in . ' seconds');
        $this->token->token = $tokenData->access_token;
        $this->token->type = $tokenData->token_type;

        return $this->token->token;
    }
}
