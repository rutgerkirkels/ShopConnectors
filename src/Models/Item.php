<?php

namespace rutgerkirkels\ShopConnectors\Models;

/**
 * Class Item
 * @package rutgerkirkels\ShopConnectors\Models
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
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
     * @return Item
     */
    public function setName(string $name) : self
    {
        $this->name = $name;
        return $this;
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
     * @return Item
     */
    public function setPriceWithTax(float $priceWithTax) : self
    {
        $this->priceWithTax = $priceWithTax;
        return $this;
    }

    /**
     * @param float $priceWithoutTax
     * @return Item
     */
    public function setPriceWithoutTax(float $priceWithoutTax) : self
    {
        $this->priceWithoutTax = $priceWithoutTax;
        return $this;
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
     * @return Item
     */
    public function setSku(string $sku) : self
    {
        $this->sku = $sku;
        return $this;
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
     * @return Item
     */
    public function setEan13(int $ean13) : self
    {
        $this->ean13 = $ean13;
        return $this;
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
     * @return Item
     */
    public function setUpc(int $upc) : self
    {
        $this->upc = $upc;
        return $this;
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
     * @return Item
     */
    public function setWeight(float $weight) : self
    {
        $this->weight = $weight;
        return $this;
    }


}