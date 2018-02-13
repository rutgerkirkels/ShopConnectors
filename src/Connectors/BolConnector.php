<?php

namespace rutgerkirkels\ShopConnectors\Connectors;


use GuzzleHttp\Client;
use rutgerkirkels\ShopConnectors\Entities\Credentials\CredentialsInterface;
use rutgerkirkels\ShopConnectors\Models\DateRange;

class BolConnector extends AbstractConnector implements ConnectorInterface
{

    protected $webservice;

    public function __construct(string $host = null, CredentialsInterface $credentials = null)
    {
        parent::__construct($host, $credentials);
        $this->webservice = new Client([
            'base_uri' => $host
        ]);
    }

    public function getOrdersByOrderDate(DateRange $dateRange)
    {
        $headers = $this->getBolHeaders('GET', '/services/rest/orders/v2');

        $response = $this->webservice->request('GET', 'services/rest/orders/v2', [
            'headers' => $headers
        ]);

        var_dump($response->getBody());die;
    }

    protected function getBolHeaders(string $method, string $uri)
    {
        $timestamp = new \DateTime();
        $signature = strtoupper($method) . PHP_EOL . PHP_EOL;
        $signature .= 'application/xml' . PHP_EOL;
        $signature .= $timestamp->format('r') . PHP_EOL;
        $signature .= 'x-bol-date:' . $timestamp->format('r') . PHP_EOL;
        $signature .= $uri;

        $headers['X-BOL-Authorization'] = $this->credentials->getPublicKey() . ':' . base64_encode(hash_hmac('SHA256', $signature, $this->credentials->getPrivateKey(), true));
        $headers['X-BOL-Date'] = $timestamp->format('r');
        $headers['Content-type'] = 'application/xml';

        return $headers;
    }
}