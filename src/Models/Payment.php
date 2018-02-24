<?php

namespace rutgerkirkels\ShopConnectors\Models;

/**
 * Class Payment
 * @package rutgerkirkels\ShopConnectors\Models
 *
 * @author Rutger Kirkels <rutger@kirkels.nl>
 */
class Payment
{
    /**
     * @var array
     */
    protected $statusses = ['paid', 'partially_paid', 'not_paid'];

    /**
     * @var string
     */
    protected $status;

    /**
     * @var
     */
    protected $type;

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return Payment
     * @throws \Exception
     */
    public function setStatus(string $status): self
    {
        if (!in_array($status, $this->statusses)) {
            throw new \Exception($status . ' is not a valid status');
        }
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $type
     * @return Payment
     */
    public function setType($type): self
    {
        $this->type = $type;
        return $this;
    }


}