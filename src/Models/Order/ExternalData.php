<?php

namespace rutgerkirkels\ShopConnectors\Models\Order;

/**
 * Class ExternalData
 * @package rutgerkirkels\ShopConnectors\Models\Order
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class ExternalData
{
    /**
     * @var int
     */
    protected $orderId;

    /**
     * @var string
     */
    protected $orderCode;

    /**
     * @var string
     */
    protected $orderIp;

    /**
     * @var string
     */
    protected $json;

    /**
     * @return int
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId
     */
    public function setOrderId(int $orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return string
     */
    public function getOrderCode(): string
    {
        return $this->orderCode;
    }

    /**
     * @param string $orderCode
     */
    public function setOrderCode(string $orderCode)
    {
        $this->orderCode = $orderCode;
    }

    /**
     * @return string
     */
    public function getOrderIp(): string
    {
        return $this->orderIp;
    }

    /**
     * @param string $orderIp
     */
    public function setOrderIp(string $orderIp)
    {
        $this->orderIp = $orderIp;
    }

    /**
     * @return string
     */
    public function getJson(): string
    {
        return $this->json;
    }

    /**
     * @param string $json
     */
    public function setJson(string $json)
    {
        $this->json = $json;
    }



}