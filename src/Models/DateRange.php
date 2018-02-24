<?php

namespace rutgerkirkels\ShopConnectors\Models;

/**
 * Class DateRange
 * @package rutgerkirkels\ShopConnectors\Models
 *
 * @author Rutger Kirkels <rutgr@kirkels.nl>
 */
class DateRange
{
    /**
     * @var \DateTime
     */
    protected $start;

    /**
     * @var \DateTime
     */
    protected $end;

    /**
     * DateRange constructor.
     * @param \DateTime|null $start
     * @param \DateTime|null $end
     */
    public function __construct(\DateTime $start = null, \DateTime $end = null)
    {
        if (is_null($start)) {
            $this->start = $this->setStart(new \DateTime());
        }
        else {
            $this->setStart($start);
        }

        if (is_null($end)) {
            $this->end = $this->setEnd(new \DateTime());
        }
        else {
            $this->setEnd($end);
        }
    }

    /**
     * @return \DateTime
     */
    public function getStart(): \DateTime
    {
        return $this->start;
    }

    /**
     * @param \DateTime $start
     * @return DateRange
     */
    public function setStart(\DateTime $start) : self
    {
        $this->start = $start;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEnd(): \DateTime
    {
        return $this->end;
    }

    /**
     * @param \DateTime $end
     * @return DateRange
     */
    public function setEnd(\DateTime $end) : self
    {
        $this->end = $end;
        return $this;
    }
}