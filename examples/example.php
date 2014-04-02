<?php

require '../vendor/autoload.php';

$test = new Ballen\FuelPlannerClient\FAPIClient(
        'ballen@bobbyallen.me', '8D0676A8', '0ed72374a96ba324896e7b5ac4cffff8'
);

// Lets configure our API credentials so we can access and query the API.
//$test->setUsername('ballen@bobbyallen.me')->setAccount('8D0676A8')->setLicense('0ed72374a96ba324896e7b5ac4cffff8');
// Lets configure our aircraft and set our departure and destination airports!
$response = $test->aircraft('A320')->from('EGSS')->to('LOWS');

$result = $response->get();

var_dump($result);

