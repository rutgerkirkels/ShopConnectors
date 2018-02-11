<?php

namespace rutgerkirkels\ShopConnectors\Helpers;

/**
 * Class DateTime
 * @package rutgerkirkels\ShopConnectors\Helpers
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class DateTime
{
    /**
     * Checks if the given timestamp contains timezone information.
     * @param string $timestamp
     * @return bool
     */
    public static function hasTimeZone(string $timestamp) {
        if (preg_match('/\d{4}-[01]\d-[0-3]\dT[0-2]\d:[0-5]\d:[0-5]\d[+-][0-2]\d:[0-5]\d|Z/', $timestamp)) {
            return true;
        }

        if (preg_match('/\d{4}-[01]\d-[0-3]\d [0-2]\d:[0-5]\d:[0-5]\d[+-][0-2]\d:[0-5]\d/', $timestamp)) {
            return true;
        }

        return false;
    }
}