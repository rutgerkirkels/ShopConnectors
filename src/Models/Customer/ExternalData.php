<?php

namespace rutgerkirkels\ShopConnectors\Models\Customer;

/**
 * Class ExternalData
 * @package rutgerkirkels\ShopConnectors\Models\Customer
 *
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class ExternalData
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }
}