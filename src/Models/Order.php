<?php

namespace rutgerkirkels\ShopConnectors\Models;


class Order
{
    /**
     * @var \DateTime
     */
    protected $date;

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
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
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
    public function setCustomer(Customer $customer): void
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
    public function setDeliveryAddress(DeliveryAddress $deliveryAddress): void
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
    public function setInvoiceAddress(InvoiceAddress $invoiceAddress): void
    {
        $this->invoiceAddress = $invoiceAddress;
    }


}