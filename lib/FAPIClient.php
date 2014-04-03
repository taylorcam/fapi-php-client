<?php

namespace Ballen\FuelPlannerClient;

class FAPIClient extends Services\FAPIClientService
{

    /**
     * @param string $user The FAPI username (eg. 'ballen@bobbyallen.me').
     * @param string $account The FAPI account ID (eg. '0ed72374a96ba324896e7b5ac4cffff8')
     * @param string $license The FAPI license key (eg.  '8D0676A8')
     */
    public function __construct($user, $account, $license)
    {
        $this->setUsername($user);
        $this->setAccount($account);
        $this->setLicense($license);

        parent::__construct();
    }

    /**
     * Return the API response for the generated request.
     * @return type
     */
    public function get()
    {
        $response = $this->send();
        return $response;
    }

    /**
     * Sets the aircraft of which you want to request the fuel plan for.
     * @param string $aircraft The ICAO code for the aircraft eg. 'A320'
     * @see http://fuelplanner.com/api.php
     * @return \Ballen\FuelPlannerClient\FAPIClient
     */
    public function aircraft($aircraft)
    {
        $this->airframeIcao = $aircraft;
        return $this;
    }

    /**
     * Sets the destination airport.
     * @param string $to The ICAO code of the destination airport eg. 'LOWS' (Salzburg)
     * @return \Ballen\FuelPlannerClient\FAPIClient
     */
    public function to($to)
    {
        $this->icaoTo = $to;
        return $this;
    }

    /**
     * Sets the departure airport.
     * @param string $from The ICAO code of the departure airport eg. 'EGSS' (London Stansted)
     * @return \Ballen\FuelPlannerClient\FAPIClient
     */
    public function from($from)
    {
        $this->icaoFrom = $from;
        return $this;
    }

    /**
     * Sets the 'Total traffic load' (optional)
     * @param int $ttl Total traffic load.
     * @return \Ballen\FuelPlannerClient\FAPIClient
     */
    public function totalTrafficLoad($ttl)
    {
        $this->ttl = $ttl;
        return $this;
    }

    /**
     * Sets the 'Operating empty weight' (optional)
     * @param int $oew Operating empty weight.
     * @return \Ballen\FuelPlannerClient\FAPIClient
     */
    public function operatingEmtpyWeight($oew)
    {
        $this->oew = $oew;
        return $this;
    }

    /**
     * Fuel capacity (i.e., maximum fuel weight)
     * @param int $fuel Maximum fuel weight.
     * @return \Ballen\FuelPlannerClient\FAPIClient
     */
    public function fuelCapacity($fuel)
    {
        $this->mtank = $fuel;
        return $this;
    }

    /**
     * Extra fuel carried (for use with Tankers etc), Use keyword AUTO to calculate tankering fuel for a round-trip. Use keyword MAX to calculate maximum tankering fuel
     * @param int $fuel The amount of extra fuel carried (in pounds (LBS))
     * @see http://fuelplanner.com/api.php
     * @return \Ballen\FuelPlannerClient\FAPIClient
     */
    public function fuelTanker($fuel)
    {
        $this->tanker = $fuel;
        return $this;
    }

    /**
     * Request that the departure and destination METAR details are also returned.
     * @param boolean $show
     * @return \Ballen\FuelPlannerClient\FAPIClient
     */
    public function metar($show = true)
    {
        if ($show) {
            $this->metar = 'YES';
        } else {
            $this->metar = 'NO';
        }
        return $this;
    }

}
