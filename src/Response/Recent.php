<?php

namespace Wjzijderveld\Youless\Api\Response;

class Recent
{
    private $power;
    private $count;

    /**
     * @param float $power in watt/hour
     * @param float $counter in watt/hour
     */
    public function __construct($power, $count)
    {
        $this->power = $power;
        $this->count = $count;
    }

    /**
     * @return float
     */
    public function getPower()
    {
        return $this->power;
    }

    /**
     * @param float
     */
    public function getCount()
    {
        return $this->count;
    }
}
