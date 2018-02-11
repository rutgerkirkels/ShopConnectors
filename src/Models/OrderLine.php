<?php

namespace rutgerkirkels\ShopConnectors\Models;

/**
 * Class OrderLine
 * @package rutgerkirkels\ShopConnectors\Models
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
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

    /**
     * OrderLine constructor.
     * @param Item|null $item
     * @param float|null $quantity
     */
    public function __construct(Item $item = null, float $quantity = null)
    {
        if (!is_null($item) && !is_null($quantity)) {
            $this->item = $item;
            $this->quantity = $quantity;
        }
    }
}