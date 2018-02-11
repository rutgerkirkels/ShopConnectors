<?php

namespace rutgerkirkels\ShopConnectors\Helpers;


class DateTime
{
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