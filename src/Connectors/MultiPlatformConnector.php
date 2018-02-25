<?php

namespace rutgerkirkels\ShopConnectors\Connectors;

use rutgerkirkels\ShopConnectors\Models\DateRange;

/**
 * Class MultiPlatformConnector
 * @package rutgerkirkels\ShopConnectors\Connectors
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class MultiPlatformConnector
{
    /**
     * @var array
     */
    protected $platforms;

    /**
     * @param AbstractConnector $platformConnector
     * @return MultiPlatformConnector
     */
    public function addPlatform(AbstractConnector $platformConnector) : self
    {
        $this->platforms[] = $platformConnector;
        return $this;
    }

    /**
     * @param DateRange $dateRange
     * @return array
     */
    public function getOrdersByOrderDate(DateRange $dateRange) : array
    {
        $orders = [];
        foreach ($this->platforms as $platform) {
            $platformOrders = $platform->getOrdersByOrderDate($dateRange);
            $orders = array_merge($orders, $platformOrders);
        }

       return $this->sortOrders($orders, 'lastupdate');
    }

    /**
     * @param array $orders
     * @param string|null $sortField
     * @return array
     */
    protected function sortOrders(array $orders, string $sortField = null)
    {
        switch (strtolower($sortField)) {
            case 'lastupdate':
                usort($orders, function($a, $b) {
                    return strcmp($a->getLastUpdate()->format('r'), $b->getLastUpdate()->format('r'));
                });
                break;

            case 'date':
            default:
                usort($orders, function($a, $b) {
                    return strcmp($a->getDate()->format('r'), $b->getDate()->format('r'));
                });
        }

        return $orders;
    }
}