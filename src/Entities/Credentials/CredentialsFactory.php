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
            $class = __NAMESPACE__ . '\\' . $shopType . 'Credentials';
            if (class_exists($class)) {
                $credentialsClass = new $class;
                return $credentialsClass;
            }
            else {
                throw new \Exception($shopType . ' is an invalid shopType');
            }

    }
}