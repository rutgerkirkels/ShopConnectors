<?php

namespace rutgerkirkels\ShopConnectors\Connectors;
use rutgerkirkels\ShopConnectors\Models\DateRange;

/**
 * Interface ConnectorInterface
 * @package rutgerkirkels\ShopConnectors\Connectors
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
interface ConnectorInterface
{
    public function getOrdersByOrderDate(DateRange $dateRange);
}