<?php

namespace Ballen\FuelPlannerClient\Types;

use \Ballen\FuelPlannerClient\Exceptions\InvalidWeightException;

class Weight
{

    /**
     * Stores the weight in pounds (lbs)
     * @var float 
     */
    protected $weight;

    /**
     * Initiate the object with the weight in pounds (lbs)
     * @param float $lbs The weight in pounds.
     */
    public function __construct($lbs = null)
    {
        if (is_null($lbs)) {
            throw new InvalidWeightException;
        }
        $this->weight = $lbs;
    }

    /**
     * Return the quanity of fuel in pounds (lbs)
     * @return float The weight conversion in pounds (lbs).
     */
    public function lbs()
    {
        return $this->weight;
    }

    /**
     * Return the quanity of fuel in kilograms (kgs)
     * @return float The weight conversion in kilograms (kgs).
     */
    public function kgs()
    {
        return ($this->weight / 2.20462);
    }

    /**
     * Return the quanity of fuel in metric tonnes (mts)
     * @return float The weight conversion in metric tonnes (mts).
     */
    public function tonnes()
    {
        return ($this->weight / 2204.62);
    }

    /**
     * Return the quanity of fuel in gallons (based on average of 6.71 lbs per gallon)
     * @return float The volume conversion in gallons.
     */
    public function gallons()
    {
        return ($this->weight / 6.71);
    }

    /**
     * Return the quanity of fuel in litres (ltrs) (based on average of 6.71lb per gallon * 4.54609).
     * @return float The volume conversion in litres.
     */
    public function litres()
    {
        return ($this->gallons() * 4.54609);
    }

}
