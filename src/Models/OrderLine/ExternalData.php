<?php

namespace rutgerkirkels\ShopConnectors\Models\OrderLine;

/**
 * Class ExternalData
 * @package rutgerkirkels\ShopConnectors\Models\OrderLine
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class ExternalData
{
    /**
     * @var string
     */
    protected $json;

    /**
     * @return string
     */
    public function getJson(): string
    {
        return $this->json;
    }

    /**
     * @param string $json
     */
    public function setJson(string $json)
    {
        $this->json = $json;
    }

}