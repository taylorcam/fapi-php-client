<?php

require '../vendor/autoload.php';

$test = new Ballen\FuelPlannerClient\FAPIClient(
        'ballen@bobbyallen.me', '8D0676A8', '0ed72374a96ba324896e7b5ac4cffff8'
);

// Lets configure our aircraft and set our departure and destination airports!
$response = $test->aircraft('A320')->from('EGLL')->to('EGPF')->metar();



echo 'Aircraft: ' . $response->get()->airframe . '<br>';
echo 'Inital heading: ' . $response->get()->initialHeading . '<br>';
echo 'Fuel usage (estimated): ' . number_format($response->get()->estimatedFuelUsage) . 'lbs / ' .number_format(($response->get()->estimatedFuelUsage / 2.20462), 2). ' kgs  / ' .number_format(($response->get()->estimatedFuelUsage / 2204.62), 5). ' metric tonnes <br>';

//echo 'Gallons of fuel (estimated): ' . Ballen\FuelPlannerClient\Services\FAPIClientService::lbsToGallons($response->get()->estimatedFuelUsage) . ' gallons.';

