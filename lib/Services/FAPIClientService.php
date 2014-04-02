<?php

namespace Ballen\FuelPlannerClient\Services;

use GuzzleHttp\Client as HttpClient;

abstract class FAPIClientService
{

    /**
     * Stores the API endpoint URL.
     */
    const FAPI_URL = 'http://fuelplanner.com/api/fuelapi.php';

    /**
     * Guzzle HTTP client instance
     * @var GuzzleHttp\Client
     */
    private $httpClient;

    /**
     * The email address that you registered with and log into FuelPlanner.com with (eg. 'ballen@bobbyallen.me')
     * @var string The API account's registered email address 
     */
    protected $user;

    /**
     * The account ID that you have been provided with from FuelPlanner.com (eg. '8D0676A8')
     * @var string The utomatically assigned API account ID. 
     */
    protected $account;

    /*
     *  The license key that you have been provided with from FuelPlanner.com (eg. '0ed72374a96ba324896e7b5ac4cffff8')
     * @var string The generated license key for FuelPlanner.com
     */
    protected $license;

    /**
     * Optional proxy server configuration settings. (eg. 'http://USERNAME:PASSWORD@proxy.local:8080')
     * @var type 
     */
    protected $proxy;

    /**
     * The API query type (default is 'FUEL')
     * @var string API request type, options are 'FUEL' and 'LIST_E'
     * @see http://fuelplanner.com/api.php
     */
    protected $query = 'FUEL';

    /**
     * The 'Rules' type
     * @var string Optional. Default is FARDOM. Can also specify INTL or JAR
     * @see http://fuelplanner.com/api.php
     */
    protected $rules = 'FARDOM';

    /**
     * Sets the aircraft type eg. (A320, A319)
     * @var string The aircraft ICAO code.
     * @see http://fuelplanner.com/api.php
     */
    protected $airframe;

    /**
     * Sets the destination airport.
     * @var string The destination airport ICAO eg. 'LOWS'
     */
    protected $icaoTo;

    /**
     * Sets the origin airport.
     * @var string The destination airport ICAO eg. 'EGSS'
     */
    protected $icaoFrom;

    /**
     * Sets the total traffic weight.
     * @var int Optional. Total traffic weight. 
     */
    protected $ttl;

    /**
     * Sets the operating empty weight.
     * @var int Optional. Operating emtpy weight. 
     */
    protected $oew;

    /**
     * Sets the maximum fuel weight.
     * @var int Optional. Maximum fuel weight (fuel capactiy) 
     */
    protected $mtank;

    /**
     * Extra fuel (in pounds) used for tanker aircraft.
     * @var int Optional. Extra fuel, in pounds (LBS). Use keyword AUTO to calculate tankering fuel for a round-trip. Use keyword MAX to calculate maximum tankering fuel 
     */
    protected $tanker;

    /**
     * Return departure and destination airport METAR details.
     * @var boolean Optional. If YES, then fueplanner will attempt to find weather data for ORIG and DEST. 
     */
    protected $metar;

    /**
     * Stores the request result as a JOSN object
     * 
     */
    private $response;

    public function __construct()
    {
        $this->httpClient = new HttpClient;
    }

    /**
     * Sends the request to the API and returns the response.
     * @return type
     */
    protected function send()
    {

        try {

            $response = $this->httpClient->post(FAPIClientService::FAPI_URL, $this->getPostRequestBody());
        } catch (GuzzleHttp\Exception\ClientErrorResponseException $e) {
            echo $e->getRequest();
            echo $e->getResponse();
        } catch (GuzzleHttp\Exception\AdapterException $e) {
            echo $e->getRequest();
            echo $e->getResponse();
        } catch (\InvalidArgumentException $e) {
            die($e->getMessage());
        }

        $this->response = FAPIClientService::XmlToJson($response->getBody());

        return $this->response;
    }

    /**
     * Set the FAPI username (email address) of which is permitted to access the API. 
     * @param string $user The FAPI username (email address) eg. 'ballen@bobbyallen.me'
     * @return \Ballen\FuelPlannerClient\FAPIClient
     */
    protected function setUsername($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Set the license key provided by FuelPlanner.com of which is permitted to access the API.
     * @param string $license The FAPI license key for your account eg. '0ed72374a96ba324896e7b5ac4cffff8'
     * @return \Ballen\FuelPlannerClient\FAPIClient
     */
    protected function setLicense($license)
    {
        $this->license = $license;
        return $this;
    }

    /**
     * Set the genearted Account ID for the FuelPlanner.com API of which is permitted to access the API.
     * @param string $account The FAPI account ID eg. '8D0676A8'
     * @return \Ballen\FuelPlannerClient\FAPIClient
     */
    protected function setAccount($account)
    {
        $this->account = $account;
        return $this;
    }

    /**
     * Builds the API POST request body.
     * @return array
     */
    private function getPostRequestBody()
    {
        $config['body'] = [
            'QUERY' => $this->query,
            'USER' => $this->user,
            'ACCOUNT' => $this->account,
            'LICENSE' => $this->license,
        ];

        if ($this->icaoFrom) {
            $config['body']['ORIG'] = $this->icaoFrom;
        }

        if ($this->icaoTo) {
            $config['body']['DEST'] = $this->icaoTo;
        }

        if ($this->airframe) {
            $config['body']['EQPT'] = $this->airframe;
        }

        if ($this->rules) {
            $config['body']['RULES'] = $this->rules;
        }

        if ($this->ttl) {
            $config['body']['TTL'] = $this->ttl;
        }

        if ($this->oew) {
            $config['body']['OEW'] = $this->oew;
        }

        if ($this->mtank) {
            $config['body']['MTANK'] = $this->mtank;
        }

        if ($this->tanker) {
            $config['body']['TANKER'] = $this->tanker;
        }

        if ($this->metar) {
            $config['body']['METAR'] = $this->metar;
        }

        if ($this->proxy) {
            $config['proxy'] = $this->proxy;
        }

        return $config;
    }

    /**
     * Converts XML to a JSON object.
     * @param string $xml The XML body
     * @return stdObject
     */
    static private function XmlToJson($xml)
    {
        $worker = str_replace(array("\n", "\r", "\t"), '', $xml);
        $worker = trim(str_replace('"', "'", $worker));
        $simpleXml = simplexml_load_string($worker)->children();

        // We replace the standard API keys with human readable keys to make it more descriptive when working in our code.
        $api_keys = array('DESCRIP', 'NM', 'HEADING_TC', 'OEW', 'TTL', 'ZFW', 'FUEL_EFU', 'FUEL_RSV', 'FUEL_TOF', 'TOW', 'LWT', 'UNDERLOAD', 'TIME_BLK', 'TIME_RSV', 'TIME_TTE', 'METAR_ORIG', 'METAR_DEST', 'MESSAGES');
        $readable_keys = array('airframe', 'distance', 'initialHeading', 'operatingEmtpyWeight', 'totalTrafficLoad', 'zeroFuelWeight', 'estimatedFuelUsage', 'reserveFuel', 'takeoffFuel', 'takeoffWeight', 'estimatedLandingWeight', 'calculatedUnderload', 'estimateTimeEnrouteBlock', 'reserveFuelTime', 'estimateTimeEnroute', 'metarDeparture', 'metarDestination', 'messages');
        $replacement = str_replace($api_keys, $readable_keys, json_encode($simpleXml));

        return json_decode($replacement);
    }

    /**
     * Helper static method to convert weight from pounds (lbs) to kilograms (kgs)
     * @param float $lbs The weight in pounds (lbs) of which to be converted to kilograms (kgs)
     * @return float The weight conversion in kilograms (kgs).
     */
    static public function lbsToKgs($lbs)
    {
        return ($lbs / 2.20462);
    }

    /**
     * Helper static method to convert weight from pounds (lbs) to metric tonnes (mt)
     * @param float $lbs The weight in pounds (lbs) of which to be converted to metric tonnes (mts)
     * @return float The weight conversion in metric tonnes (mts).
     */
    static public function lbsToTonnes($lbs)
    {
        return ($lbs / 2204.62);
    }

    /**
     * Helper static method to convert weight from pounds (lbs) to gallons (based on average of 6.71lb per gallon).
     * @param float $lbs The weight in pounds (lbs) of which to be converted to gallons.
     * @return float The volume conversion in gallons.
     */
    static public function lbsToGallons($lbs)
    {
        return ($lbs / 6.71);
    }

    /**
     * Helper static method to convert weight from pounds (lbs) to litres (ltrs) (based on average of 6.71lb per gallon * 4.54609).
     * @param float $lbs The weight in pounds (lbs) of which to be converted to litres.
     * @return float The volume conversion in litres.
     */
    static public function lbsToLitres($lbs)
    {
        $gallons = self::lbsToGallons($lbs);
        return ($gallons * 4.54609);
    }

}
