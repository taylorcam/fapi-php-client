<?php

namespace Ballen\FuelPlannerClient\Services;

use GuzzleHttp\Client as HttpClient;
use Ballen\FuelPlannerClient\Types\Aircraft;
use Ballen\FuelPlannerClient\Types\Weight;
use Ballen\FuelPlannerClient\Types\Distance;

abstract class FAPIClientService
{

    /**
     * Stores the API endpoint URL.
     */
    const FAPI_URL = 'http://fuelplanner.com/api/fuelapi.php';

    /**
     * Enables testing using some example data without having to register for a full account.
     */
    const DEBUG = true;

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
    protected $airframeIcao;

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

        if (!self::DEBUG) {
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
            $this->response = FAPIClientService::remapKeys(json_decode(FAPIClientService::XmlToJson($response->getBody())), $this->airframeIcao);
        } else {

            // We will use out example data instead of requesting it via the API.
            $this->response = FAPIClientService::remapKeys('{"DESCRIP":"Airbus A320","NM":"298","HEADING_TC":"333","OEW":"93051","TTL":"31946","ZFW":"124997","FUEL_EFU":"6126","FUEL_RSV":"6898","FUEL_TOF":"13024","TOW":"138021","LWT":"131895","UNDERLOAD":"24047","TIME_BLK":"01:06","TIME_RSV":"01:15","TIME_TTE":"02:21","METAR_ORIG":"EGLL 021120Z 07008KT 9000 NSC 17\/10 Q1004 NOSIG","METAR_DEST":"EGPF 021120Z 07014KT 9999 OVC012 08\/05 Q1008","MESSAGES":{"MESG":"TTL VIA SEATS 179"}}', $this->airframeIcao);
        }

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
            $config['body']['EQPT'] = $this->airframeIcao;
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
        return $simpleXml;
    }

    /**
     * Here we remap the API response keys to friendlier, more read-able keys we also create new 'weight' objects to allow nice conversions.
     * @param string $data API response data.
     * @return \stdClass
     */
    static private function remapKeys($data, $airframeIcao)
    {
        $api_keys = array('DESCRIP', 'NM', 'HEADING_TC', 'OEW', 'TTL', 'ZFW', 'FUEL_EFU', 'FUEL_RSV', 'FUEL_TOF', 'TOW', 'LWT', 'UNDERLOAD', 'TIME_BLK', 'TIME_RSV', 'TIME_TTE', 'METAR_ORIG', 'METAR_DEST', 'MESSAGES');
        $readable_keys = array('airframe', 'distance', 'initialHeading', 'operatingEmtpyWeight', 'totalTrafficLoad', 'zeroFuelWeight', 'estimatedFuelUsage', 'reserveFuel', 'takeoffFuel', 'takeoffWeight', 'estimatedLandingWeight', 'calculatedUnderload', 'estimateTimeEnrouteBlock', 'reserveFuelTime', 'estimateTimeEnroute', 'metarDeparture', 'metarDestination', 'messages');
        $replacement = str_replace($api_keys, $readable_keys, $data);

        $object = json_decode($replacement);

        $object->distance = new Distance($object->distance);
        $object->airframe = new Aircraft($airframeIcao, $object->airframe);
        $object->operatingEmtpyWeight = new Weight($object->operatingEmtpyWeight);
        $object->totalTrafficLoad = new Weight($object->totalTrafficLoad);
        $object->zeroFuelWeight = new Weight($object->zeroFuelWeight);
        $object->estimatedFuelUsage = new Weight($object->estimatedFuelUsage);
        $object->reserveFuel = new Weight($object->reserveFuel);
        $object->takeoffFuel = new Weight($object->takeoffFuel);
        $object->takeoffWeight = new Weight($object->takeoffWeight);
        $object->estimatedLandingWeight = new Weight($object->estimatedLandingWeight);
        $object->calculatedUnderload = new Weight($object->calculatedUnderload);

        return $object;
    }

}
