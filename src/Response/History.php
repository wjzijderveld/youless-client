<?php

namespace Wjzijderveld\Youless\Api\Response;

use DateTime;

class History
{
    private $measuredFrom;
    private $values;
    private $unit;
    private $deltaInSeconds;

    /**
     * @param string  $unit (watt|kWh)
     * @param integer $deltaInSeconds Number of seconds between each value
     */
    public function __construct(DateTime $measuredFrom, array $values, $unit, $deltaInSeconds)
    {
        $this->measuredFrom   = $measuredFrom;
        $this->values         = $values;
        $this->unit           = $unit;
        $this->deltaInSeconds = $deltaInSeconds;
    }

    public function getValuesInWatt()
    {
        if ('kWh' === $this->unit) {
            return array_map(function ($value) {
                return $value * 1000;
            }, $this->values);
        }

        return $this->values;
    }

    /**
     * @return \DateTime
     */
    public function getMeasuredFrom()
    {
        return $this->measuredFrom;
    }

    /**
     * @return integer
     */
    public function getDeltaInSeconds()
    {
        return $this->deltaInSeconds;
    }
}
