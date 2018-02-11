<?php

namespace rutgerkirkels\ShopConnectors\Models;


class Item
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var float
     */
    protected $priceWithoutTax;

    /**
     * @var float
     */
    protected $priceWithTax;

    /**
     * @var string
     */
    protected $sku;

    /**
     * @var int
     */
    protected $ean13;

    /**
     * @var int
     */
    protected $upc;

    /**
     * @var float
     */
    protected $weight;



    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return float
     */
    public function getPriceWithoutTax(): float
    {
        return $this->priceWithoutTax;
    }

    /**
     * @return float
     */
    public function getPriceWithTax(): float
    {
        return $this->priceWithTax;
    }

    /**
     * @param float $priceWithTax
     */
    public function setPriceWithTax(float $priceWithTax): void
    {
        $this->priceWithTax = $priceWithTax;
    }

    /**
     * @param float $priceWithoutTax
     */
    public function setPriceWithoutTax(float $priceWithoutTax): void
    {
        $this->priceWithoutTax = $priceWithoutTax;
    }

    /**
     * @return string
     */
    public function getSku(): string
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     */
    public function setSku(string $sku): void
    {
        $this->sku = $sku;
    }

    /**
     * @return int
     */
    public function getEan13(): int
    {
        return $this->ean13;
    }

    /**
     * @param int $ean13
     */
    public function setEan13(int $ean13): void
    {
        $this->ean13 = $ean13;
    }

    /**
     * @return int
     */
    public function getUpc(): int
    {
        return $this->upc;
    }

    /**
     * @param int $upc
     */
    public function setUpc(int $upc): void
    {
        $this->upc = $upc;
    }

    /**
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    /**
     * @param float $weight
     */
    public function setWeight(float $weight): void
    {
        $this->weight = $weight;
    }


}