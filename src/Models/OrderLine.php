<?php

namespace rutgerkirkels\ShopConnectors\Models;


class OrderLine
{
    /**
     * @var float
     */
    protected $quantity;

    /**
     * @var Item
     */
    protected $item;

    public function __construct(Item $item = null, float $quantity = null)
    {
        if (!is_null($item) && !is_null($quantity)) {
            $this->item = $item;
            $this->quantity = $quantity;
        }
    }
}