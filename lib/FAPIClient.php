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
        return $this->send();
    }

    /**
     * Set the FAPI username (email address) of which is permitted to access the API. 
     * @param string $user The FAPI username (email address) eg. 'ballen@bobbyallen.me'
     * @return \Ballen\FuelPlannerClient\FAPIClient
     */
    private function setUsername($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Set the license key provided by FuelPlanner.com of which is permitted to access the API.
     * @param string $license The FAPI license key for your account eg. '0ed72374a96ba324896e7b5ac4cffff8'
     * @return \Ballen\FuelPlannerClient\FAPIClient
     */
    private function setLicense($license)
    {
        $this->license = $license;
        return $this;
    }

    /**
     * Set the genearted Account ID for the FuelPlanner.com API of which is permitted to access the API.
     * @param string $account The FAPI account ID eg. '8D0676A8'
     * @return \Ballen\FuelPlannerClient\FAPIClient
     */
    private function setAccount($account)
    {
        $this->account = $account;
        return $this;
    }

    /**
     * Sets the aircraft of which you want to request the fuel plan for.
     * @param string $aircraft The ICAO code for the aircraft eg. 'A320'
     * @see http://fuelplanner.com/api.php
     * @return \Ballen\FuelPlannerClient\FAPIClient
     */
    public function aircraft($aircraft)
    {
        $this->airframe = $aircraft;
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

}
