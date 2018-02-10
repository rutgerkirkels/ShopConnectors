<?php

namespace rutgerkirkels\ShopConnectors\Connectors;
use GuzzleHttp\Client;
use rutgerkirkels\ShopConnectors\Entities\Credentials\CredentialsInterface;
use rutgerkirkels\ShopConnectors\Models\Customer;
use rutgerkirkels\ShopConnectors\Models\DateRange;
use rutgerkirkels\ShopConnectors\Models\Order;

/**
 * Class PrestashopConnector
 * @package rutgerkirkels\ShopConnectors\Connectors
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class PrestashopConnector extends AbstractConnector implements ConnectorInterface
{

    /**
     * @var \PrestaShopWebservice
     */
    protected $webservice;

    public function __construct(string $host = null, CredentialsInterface $credentials = null)
    {
        parent::__construct($host, $credentials);
        $this->webservice = new Client([
            'base_uri' => $this->host . '/api/',
            'auth' => [$this->credentials->getKey(),'']
        ]);
    }

    public function getOrders(DateRange $dateRange = null)
    {
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
            $order->setDate(new \DateTime($psOrder->date_add));
            $order->setCustomer($this->getCustomer($psOrder->id_customer));

            $orders[] = $order;
        }

        return $orders;
    }

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
    protected function getOrderDetails(int $orderId) {
        $opts = [
            'resource' => 'order_details',
            'id' => $orderId
        ];

        $xml = $this->webservice->get($opts);

        $resource = $xml->children()->children();

        return $resource;

    }
}