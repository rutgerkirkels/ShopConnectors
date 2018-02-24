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
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
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
     */
    public function setLastUpdate(\DateTime $lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;
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
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;
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
     */
    public function setDeliveryAddress(DeliveryAddress $deliveryAddress)
    {
        $this->deliveryAddress = $deliveryAddress;
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
     */
    public function setInvoiceAddress(InvoiceAddress $invoiceAddress)
    {
        $this->invoiceAddress = $invoiceAddress;
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
     */
    public function setOrderLines(array $orderLines)
    {
        $this->orderLines = $orderLines;
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
     */
    public function setExternalData(ExternalData $externalData)
    {
        $this->externalData = $externalData;
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
     */
    public function setPayment(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function getChecksum() {
        // TODO: generate checksum
    }
}