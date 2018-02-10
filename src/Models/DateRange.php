<?php


namespace rutgerkirkels\ShopConnectors\Models;


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
     */
    public function setStart(\DateTime $start): void
    {
        $this->start = $start;
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
     */
    public function setEnd(\DateTime $end): void
    {
        $this->end = $end;
    }
}