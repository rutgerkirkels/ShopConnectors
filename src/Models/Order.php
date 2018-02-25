<?php

namespace rutgerkirkels\ShopConnectors\Models;

use rutgerkirkels\ShopConnectors\Models\Order\ExternalData;

/**
 * Class Order
 * @package rutgerkirkels\ShopConnectors\Models
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class Order extends AbstractModel
{
    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var \DateTime
     */
    protected $lastUpdate;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var DeliveryAddress
     */
    protected $deliveryAddress;

    /**
     * @var InvoiceAddress
     */
    protected $invoiceAddress;

    /**
     * @var array
     */
    protected $orderLines;

    /**
     * @var ExternalData
     */
    protected $externalData;

    /**
     * @var Payment
     */
    protected $payment;

    /**
     * @var string
     */
    protected $platform;

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return Order
     */
    public function setDate(\DateTime $date) : self
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastUpdate(): \DateTime
    {
        return $this->lastUpdate;
    }

    /**
     * @param \DateTime $lastUpdate
     * @return Order
     */
    public function setLastUpdate(\DateTime $lastUpdate) : self
    {
        $this->lastUpdate = $lastUpdate;
        return $this;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;

    }

    /**
     * @param Customer $customer
     * @return Order
     */
    public function setCustomer(Customer $customer) : self
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @return DeliveryAddress
     */
    public function getDeliveryAddress(): DeliveryAddress
    {
        return $this->deliveryAddress;
    }

    /**
     * @param DeliveryAddress $deliveryAddress
     * @return Order
     */
    public function setDeliveryAddress(DeliveryAddress $deliveryAddress) : self
    {
        $this->deliveryAddress = $deliveryAddress;
        return $this;
    }

    /**
     * @return InvoiceAddress
     */
    public function getInvoiceAddress(): InvoiceAddress
    {
        return $this->invoiceAddress;
    }

    /**
     * @param InvoiceAddress $invoiceAddress
     * @return Order
     */
    public function setInvoiceAddress(InvoiceAddress $invoiceAddress) : self
    {
        $this->invoiceAddress = $invoiceAddress;
        return $this;
    }

    /**
     * @return array
     */
    public function getOrderLines(): array
    {
        return $this->orderLines;

    }

    /**
     * @param array $orderLines
     * @return Order
     */
    public function setOrderLines(array $orderLines) : self
    {
        $this->orderLines = $orderLines;
        return $this;
    }

    /**
     * @return ExternalData
     */
    public function getExternalData(): ExternalData
    {
        return $this->externalData;
    }

    /**
     * @param ExternalData $externalData
     * @return Order
     */
    public function setExternalData(ExternalData $externalData) : self
    {
        $this->externalData = $externalData;
        return $this;
    }

    /**
     * @return Payment
     */
    public function getPayment(): Payment
    {
        return $this->payment;
    }

    /**
     * @param Payment $payment
     * @return Order
     */
    public function setPayment(Payment $payment) : self
    {
        $this->payment = $payment;
        return $this;
    }

    /**
     * @return string
     */
    public function getPlatform(): string
    {
        return $this->platform;
    }

    /**
     * @param string $platform
     * @return Order
     */
    public function setPlatform(string $platform): self
    {
        $this->platform = $platform;
        return $this;
    }



    public function getChecksum() {
        // TODO: generate checksum
    }
}