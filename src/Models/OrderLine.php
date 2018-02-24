<?php

namespace rutgerkirkels\ShopConnectors\Models;

use rutgerkirkels\ShopConnectors\Models\OrderLine\ExternalData;

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
     * @var ExternalData
     */
    protected $externalData;

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

    /**
     * @return ExternalData
     */
    public function getExternalData(): ExternalData
    {
        return $this->externalData;
    }

    /**
     * @param ExternalData $externalData
     * @return OrderLine
     */
    public function setExternalData(ExternalData $externalData) : self
    {
        $this->externalData = $externalData;
        return $this;
    }


}