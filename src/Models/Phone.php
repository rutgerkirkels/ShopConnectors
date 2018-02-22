<?php

namespace rutgerkirkels\ShopConnectors\Models;

/**
 * Class Phone
 * @package rutgerkirkels\ShopConnectors\Models
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class Phone extends AbstractModel
{
    /**
     * @var string
     */
    protected $number;

    /**
     * @var string
     */
    protected $type;

    /**
     * Phone constructor.
     * @param string $number
     * @param string $type
     */
    public function __construct(string $number, string $type = 'landline')
    {
        $this->setNumber($number);
        $this->setType($type);
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @param string $number
     */
    public function setNumber(string $number)
    {
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }


}