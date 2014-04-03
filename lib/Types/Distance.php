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
    public function __construct($nm = null)
    {
        if (is_null($nm)) {
            throw new InvalidDistanceException;
        }
        $this->distance = $nm;
    }

    /**
     * Return the distance in nautical miles (nm)
     * @return float Distance convertsion in nautical miles (nm)
     */
    public function nm()
    {
        return $this->distance;
    }

    /**
     * Return the distance in standard miles (m)
     * @return float Distance convertsion in standard 'road' miles (mi)
     */
    public function mi()
    {
        return ($this->km() * 0.621371192);
    }

    /**
     * Return the distance in kilometers (km)
     * @return float Distance convertsion in kilometers (km)
     */
    public function km()
    {
        return ($this->distance * 1.85200);
    }

    /**
     * Return the distance in meters (m)
     * @return float Distance convertsion in meters (m)
     */
    public function m()
    {
        return ($this->km() * 1000);
    }

    /**
     * Return the distance in feet (ft)
     * @return float Distance convertsion in feet (ft)
     */
    public function ft()
    {
        return ($this->m() / 0.3048);
    }

}
