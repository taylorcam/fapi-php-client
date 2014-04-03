<?php

namespace Ballen\FuelPlannerClient\Types;

use \Ballen\FuelPlannerClient\Exceptions\InvalidAircraftException;

class Aircraft
{

    /**
     * Stores the aircraft name.
     * @var string
     */
    protected $name;

    /**
     * Stores the aircraft ICAO code.
     * @var string
     */
    protected $icao;

    /**
     * Initiate the object with the aircraft name.
     * @param string The aircraft name.
     */
    public function __construct($icao = null, $aircraft = null)
    {
        if (is_null($aircraft) or is_null($icao)) {
            throw new InvalidAircraftException;
        }
        $this->name = $aircraft;
        $this->icao = $icao;
    }

    /**
     * Return the aircraft ICAO code eg. 'A320'
     * @return string The aircraft ICAO code
     */
    public function icao()
    {
        return strtoupper($this->icao);
    }

    /**
     * Return the aircraft friendly name eg. 'Airbus A320'
     * @return string The aircraft manufacter and model.
     */
    public function name()
    {
        return $this->name;
    }

}
