<?php

namespace Ballen\FuelPlannerClient\Types;

use \Ballen\FuelPlannerClient\Exceptions\InvalidMetarException;

class Metar
{

    /**
     * METAR report for location.
     * @var string  METAR report location for example departure airport ICAO (eg. 'EGSS')
     */
    private $location;

    /**
     * Stores the METAR report time.
     * @var type 
     */
    private $time;

    /**
     * Stores the day of the month.
     * @var int Day of the month 
     */
    private $day;

    /**
     * ???
     * @var type 
     */
    private $auto = false;

    /**
     * Stores the cloud cover type
     * @var array 
     */
    private $cloudCover = array();

    /**
     * Stores runway infomation.
     * @var array 
     */
    private $runways = array();

    /**
     * Stores weather conditions.
     * @var array 
     */
    private $weather = array();

    /**
     * Stores weather type descriptions.
     * @var array 
     */
    private $wxTypes = array(
        'MI' => 'Shallow',
        'PR' => 'Partial',
        'BC' => 'Low drifting',
        'BL' => 'Blowing',
        'SH' => 'Showers',
        'TS' => 'Thunderstorm',
        'FZ' => 'Freezing',
        'DZ' => 'Drizzle',
        'RA' => 'Rain',
        'SN' => 'Snow',
        'SG' => 'Snow Grains',
        'IC' => 'Ice crystals',
        'PL' => 'Ice pellets',
        'GR' => 'Hail',
        'GS' => 'Small hail',
        'UP' => 'Unknown',
        'BR' => 'Mist',
        'FG' => 'Fog',
        'FU' => 'Smoke',
        'VA' => 'Volcanic ash',
        'DU' => 'Widespread dust',
        'SA' => 'Sand',
        'HZ' => 'Haze',
        'PY' => 'Spray',
        'PO' => 'Well developed dust / Sand whirls',
        'SQ' => 'Squalls',
        'FC' => 'Funnel clouds inc. tornadoes or waterspouts',
        'SS' => 'Sandstorm',
        'DS' => 'Duststorm');

    public function __construct($code = null)
    {

        if (is_null($code)){
            throw new InvalidMetarException;
        }
        $this->readFromCode($code);
    }

    /**
     * Main METAR decoder, initates the decoding of a standard METAR string.
     * @param string $code Formatted METAR string (eg. 'EGPF 021120Z 07014KT 9999 OVC012 08/05 Q1008')
     */
    private function readFromCode($code)
    {

        $pieces = explode(' ', $code);
        $pos = 0;
        if ($pieces[0] == 'METAR') {
            $pos++;
        }
        if (strlen($pieces[$pos]) != 4) { // skip COR and similar
            $pos++;
        }
        $this->setLocation($pieces[$pos]);
        $pos++;
        $this->setDayOfMonth($pieces[$pos]{0} . $pieces[$pos]{1});
        $this->setZuluTime(substr($pieces[$pos], 2, 4));
        $c = count($pieces);
        for ($pos++; $pos < $c; $pos++) {
            $piece = $pieces[$pos];
            if ($piece == "RMK")
                break; // we are not interested in remarks
            $this->checkFormat($piece);
        }
    }

    public function checkFormat($code)
    {
        $matches = array();
        if ($code == 'AUTO') {
            $this->setIsAuto(true);
        }
        if (preg_match('#^([0-9]{3})([0-9]{2})(G([0-9]{2}))?(KT|MPS)$#', $code, $matches)) { //WEATHER dddssKT or dddssGggKT
            $this->setWindDirection($matches[1]);
            $this->setWindSpeed($matches[2], $matches[5]);
            if ($matches[3])
                $this->setWindGusts($matches[4]);
            return;
        }
        if (preg_match('#^(M?[0-9]{2,})/(M?[0-9]{2,})$#', $code, $matches)) { //TEMP/DEW TT/DD negative M03
            $temp = (float) $matches[1];
            if ($matches[1]{0} == 'M')
                $temp = ((float) substr($matches[1], 1)) * -1;

            $this->setTemperature($temp);

            $dew = (float) $matches[2];
            if ($matches[2]{0} == 'M')
                $dew = ((float) substr($matches[2], 1)) * -1;
            $this->setDewPoint($dew);
            return;
        }
        if (preg_match('#^(A|Q)([0-9]{4})$#', $code, $matches)) { //QNH
            $this->setQNH((float) $matches[2], $matches[1] == 'Q' ? 'hPa' : 'inHg');
            return;
        }

        if (preg_match('#^([0-9]{3})V([0-9]{3})$#', $code, $matches)) {
            $this->setWindDirectionVariable(array($matches[1], $matches[2]));
            return;
        }
        if (preg_match('#^VRB([0-9]{2})KT$#', $code, $matches)) {
            $this->setWindSpeedVariable($matches[1]);
            return;
        }
        if (preg_match('#^([0-9]{4})|([0-9]{1,4})SM$#', $code, $matches)) {
            if (strlen(isset($matches[2]) && $matches[2]) > 0) {
                $this->setVisibility((float) $matches[2] * 1609.34);
            } else if (isset($matches[1]) && $matches[1] == '9999') {
                $this->setVisibility('> 10000');
            } else {
                $this->setVisibility($matches[1]);
            }
            return;
        } else if (preg_match('#^CAVOK$#', $code, $matches)) {
            $this->setVisibility('> 10000');
            $this->addWeather("CAVOK");
        }

        if (preg_match('#^(SKC|CLR|FEW|SCT|BKN|OVC|VV)([0-9]{3})(CB|TCU)?$#', $code, $matches)) {
            $this->addCloudCover($matches[1], ((float) $matches[2]) * 100, $matches[3]);
            return;
        }
        if (preg_match('#^(R[A-Z0-9]{2,3})/([0-9]{4})(V([0-9]{4}))?(FT)?$#', $code, $matches)) {

            $range = array('exact' => (float) $matches[2], 'unit' => $matches[5] ? 'FT' : 'M');
            if (isset($matches[3])) {
                $range = array('from' => (float) $matches[2], 'to' => (float) $matches[4], 'unit' => $matches[5] ? 'FT' : 'M');
            }
            $this->addRunwayVisualRange($matches[1], $range);
            return;
        }

        if (preg_match('#^(\+|\-|VC)?(' . implode('|', array_keys($this->wxTypes)) . ')(' . implode('|', array_keys($this->wxTypes)) . ')?$#', $code, $matches)) {
            $text = array();
            switch ($matches[1]) {
                case '+':
                    $text[] = 'Heavy';
                    break;
                case '-':
                    $text[] = 'Light';
                    break;
                case 'VC':
                    $text[] = 'Vicinity';
                    break;
                default:

                    break;
            }

            if (isset($matches[2]) && $matches[2]) {
                $text[] = $this->wxTypes[$matches[2]];
            }
            if (isset($matches[3]) && $matches[3]) {
                $text[] = $this->wxTypes[$matches[3]];
            }
            $this->addWeather(implode(' ', $text));
            return;
        }
    }

    public function addWeather($weather)
    {

        $this->weather[] = $weather;
    }

    public function getWeather()
    {
        return $this->weather ? $this->weather : array("CLEAR");
    }

    public function addRunwayVisualRange($runway, $range)
    {
        $this->runways[$runway] = $range;
    }

    public function getRunwayVisualRange($runway)
    {
        return isset($this->runways[$runway]) ? $this->runways[$runway] : null;
    }

    public function addCloudCover($type, $level, $significant)
    {

        $this->cloudCover[] = array('type' => $type, 'level' => $level, 'significant' => $significant);
    }

    public function getCloudCover()
    {
        return $this->cloudCover;
    }

    public function setVisibility($val)
    {

        $this->visibility = $val;
    }

    public function getVisibility()
    {
        return $this->visibility;
    }

    public function setWindSpeedVariable($val)
    {
        $this->windSpeedDirectionVariable = (float) $val;
    }

    public function getWindSpeedVariable()
    {

        return $this->windSpeedDirectionVariable;
    }

    public function setWindDirectionVariable($val)
    {
        $this->windDirectionVariable = $val;
    }

    public function getWindDirectionVariable()
    {
        return $this->windDirectionVariable;
    }

    public function setQNH($val, $unit)
    {
        if ($unit != 'hPa' && $unit != 'inHg') {
            throw new Exception('Unknown unit for QNH (only hPa or inHg)');
        }
        if ($unit == 'inHg') {
            $this->QNH = $this->translateInHgToHPa($val);
        }
        $this->QNH = $val;
    }

    private function translateInHgToHPa($val)
    {
        return round($val / 100 * 33.86389);
    }

    public function getQNH()
    {
        return $this->QNH;
    }

    public function setTemperature($val)
    {
        $this->temperature = $val;
    }

    public function getTemperature()
    {
        return $this->temperature;
    }

    public function setDewPoint($val)
    {
        $this->dewPoint = $val;
    }

    public function getDewPoint()
    {
        return $this->dewPoint;
    }

    public function setWindGusts($val)
    {
        $this->windGusts = (float) $val;
    }

    public function getWindGusts()
    {
        return $this->windGusts ? $this->windGusts : 0;
    }

    public function setWindDirection($val)
    {
        $this->windDirection = (float) $val;
    }

    public function getWindDirection()
    {
        return $this->windDirection;
    }

    public function setWindSpeed($speed, $unit)
    {
        $speedKT = (float) $speed;
        if ($unit == 'MPS') {
            $speedKT = 0.00031965819613457 * $speedKT;
        }
        $this->windSpeedKT = $speedKT;
    }

    public function getWindSpeed()
    {
        return $this->windSpeedKT;
    }

    public function setIsAuto($auto)
    {
        $this->auto = $auto;
    }

    public function isAuto()
    {
        return $this->auto;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function setLocation($loc)
    {
        if (strlen($loc) != 4) {
            throw new Exception('Invalid location');
        }
        $this->location = $loc;
    }

    public function getDayOfMonth()
    {
        return $this->day;
    }

    public function setDayOfMonth($day)
    {
        if ($day < 1 || $day > 31) {
            throw new Exception('Invalid day of month');
        }
        $this->day = $day;
    }

    public function getZuluTime()
    {
        return $this->time;
    }

    public function setZuluTime($time)
    {
        $this->time = $time;
    }

}