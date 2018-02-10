<?php

namespace rutgerkirkels\ShopConnectors\Entities\Credentials;

/**
 * Class CredentialsFactory
 * @package rutgerkirkels\ShopConnectors\Entities\Credentials
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class CredentialsFactory
{
    public static function build($shopType)
    {
        try {
            $class = __NAMESPACE__ . '\\' . $shopType . 'Credentials';
            $credentialsClass = new $class;
        }
        catch (\Exception $exception) {
            error_log($exception->getMessage(), E_ERROR);
        }

        return $credentialsClass;
    }
}