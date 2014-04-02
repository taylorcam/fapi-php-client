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
echo 'Aircraft: ' . $response->airframe . '<br>';
echo 'Inital heading: ' . $response->initialHeading . '<br>';
echo 'Fuel usage (estimated): ' . number_format($response->estimatedFuelUsage->lbs()) . 'lbs / ' . number_format($response->estimatedFuelUsage->kgs(), 2) . ' kgs  / ' . number_format($response->estimatedFuelUsage->tonnes(), 5) . ' metric tonnes.<br>';
echo 'Gallons of fuel (estimated): ' . $response->estimatedFuelUsage->gallons() . ' gallons.';

