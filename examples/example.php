<?php

require '../vendor/autoload.php';

$test = new Ballen\FuelPlannerClient\FAPIClient(
        'ballen@bobbyallen.me', '8D0676A8', '0ed72374a96ba324896e7b5ac4cffff8'
);

// Lets configure our aircraft and set our departure and destination airports!
$response = $test->aircraft('A320')->from('EGLL')->to('EGPF')->metar();

var_dump($response->get());


//var_dump($test->supportedAircraft());

