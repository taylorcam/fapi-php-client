<?php

require '../vendor/autoload.php';

use Ballen\FuelPlannerClient\FAPIClient;

$fuel_client = new FAPIClient(
        'ballen@bobbyallen.me', // Your 'user' account (email addres)
        '8D0676A8', // Your generated account ID.
        '0ed72374a96ba324896e7b5ac4cffff8' // Your license key.
);

$response = $fuel_client->aircraft('A320') // Set your aircraft type.
        ->from('EGLL') // Set the departure airport.
        ->to('EGPF') // Set the destination airport.
        ->metar() // Also request both departure and destination.
        ->get(); // We now get the response from the web service!


/**
 * Now we'll display some pretty standard infomation as an example...
 */
echo '<h2>Airframe Details</h2>';
echo 'Aircraft: ' . $response->airframe->name() . ' (' . $response->airframe->icao() . ')<br>';
echo 'Inital heading: ' . $response->initialHeading . '<br>';

echo '<h2>Fuel (weight)</h2>';
echo 'Fuel usage (estimated): ' . number_format($response->estimatedFuelUsage->lbs()) . ' lbs / ' . number_format($response->estimatedFuelUsage->kgs(), 2) . ' kgs  / ' . number_format($response->estimatedFuelUsage->tonnes(), 5) . ' metric tonnes.<br>';

echo '<h2>Fuel (volume)</h2>';
echo 'Fuel usage (estimated): ' . number_format($response->estimatedFuelUsage->gallons(), 2) . ' gallons / ' . number_format($response->estimatedFuelUsage->litres(), 2) . ' litres.<br><br>';

echo '<h2>Distance</h2>';
echo 'Total flight distance: ' . $response->distance->nm() . ' nautical miles which amounts to ' . number_format($response->distance->km(), 2) . ' kilometers (' . number_format($response->distance->mi(), 2) . ' road miles) and as meters a total of ' . number_format($response->distance->m(), 2) . 'm (' . number_format($response->distance->ft(), 2) . ' ft).';

echo '<h2>METAR</h2>';
echo '<PRE>';
echo 'Departure: ' . $response->metarDeparture . PHP_EOL;
echo 'Destination: ' . $response->metarDestination;
echo '</PRE>';

echo '<h2>Fuel Time</h2>';
echo 'Estimated time (normal): ' . $response->estimateTimeEnrouteBlock . ' <small>(HH:MM)</small><br>';
echo 'Estimated time (reserve): ' . $response->reserveFuelTime . ' <small>(HH:MM)</small><br>';
echo 'Esitmated time (total): ' . $response->estimateTimeEnroute . ' <small>(HH:MM)</small>';

echo '<h2>Other options and data:</h2>';
echo 'Total traffic load: ' . $response->totalTrafficLoad->lbs() . 'lbs (' . $response->totalTrafficLoad->kgs() . ' kgs / ' . number_format($response->totalTrafficLoad->tonnes(), 1) . ' tonnes)<br>';
echo 'Operating empty weight: ' . $response->operatingEmtpyWeight->lbs() . 'lbs (' . $response->operatingEmtpyWeight->kgs() . ' kgs / ' . number_format($response->operatingEmtpyWeight->tonnes(), 1) . ' tonnes)<br>';
echo 'Zero fuel weight <small>(Aircraft plus passengers and baggage, minus fuel)</small>: ' . $response->zeroFuelWeight->lbs() . 'lbs (' . $response->zeroFuelWeight->kgs() . ' kgs / ' . number_format($response->zeroFuelWeight->tonnes(), 1) . ' tonnes)<br>';
echo 'Take-off weight: ' . $response->takeoffWeight->lbs() . 'lbs (' . $response->takeoffWeight->kgs() . ' kgs / ' . number_format($response->takeoffWeight->tonnes(), 1) . ' tonnes)<br>';
echo 'Reserve fuel: ' . $response->reserveFuel->lbs() . 'lbs (' . $response->reserveFuel->kgs() . ' kgs / ' . number_format($response->reserveFuel->tonnes(), 1) . ' tonnes)<br>';
echo 'Takeoff fuel: ' . $response->takeoffFuel->lbs() . 'lbs (' . $response->takeoffFuel->kgs() . ' kgs / ' . number_format($response->takeoffFuel->tonnes(), 1) . ' tonnes)<br>';
echo 'Estimated landing weight: ' . $response->estimatedLandingWeight->lbs() . 'lbs (' . $response->estimatedLandingWeight->kgs() . ' kgs / ' . number_format($response->estimatedLandingWeight->tonnes(), 1) . ' tonnes)<br>';
echo 'Calculated underload: ' . $response->calculatedUnderload->lbs() . 'lbs (' . $response->calculatedUnderload->kgs() . ' kgs / ' . number_format($response->calculatedUnderload->tonnes(), 1) . ' tonnes)<br>';