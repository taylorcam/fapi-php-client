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

        /**
         * For debugging reasons you can uncomment this to 'view source' the XML layout.
         */
        //die($response->getBody());

        return $response->xml();
    }

    /**
     * Builds the API POST request body.
     * @return array
     */
    private function getPostRequestBody()
    {
        $config['body'] = [
            'QUERY' => $this->query,
            'RULES' => $this->rules,
            'USER' => $this->user,
            'ACCOUNT' => $this->account,
            'LICENSE' => $this->license,
            'ORIG' => $this->icaoFrom,
            'DEST' => $this->icaoTo,
            'EQPT' => $this->airframe,
        ];

        if ($this->proxy) {
            $config['proxy'] = $this->proxy;
        }

        return $config;
    }

}
