<?php

namespace Ballen\FuelPlannerClient\Types;

use \Ballen\FuelPlannerClient\Exceptions\InvalidDistanceException;

class Distance
{

    /**
     * Stores the distance in nautical miles (nm)
     * @var float 
     */
    protected $distance;

    /**
     * Initiate the object with the distance (in nautical miles)
     * @param float $nm The distance in nautical miles.
     */
    public function __construct($nm)
    {
        if (is_null($nm)) {
            throw new InvalidDistanceException;
        }
        $this->distance = $nm;
    }

    /**
     * Return the quanity of fuel in pounds (lbs)
     * @return float The weight conversion in pounds (lbs).
     */
    public function nm()
    {
        return $this->distance;
    }

}
