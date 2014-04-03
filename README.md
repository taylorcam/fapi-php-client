FuelPlanner.com (FAPI) PHP client library
===========================================

A PHP client library for the FuelPlanner.com API, this library enables a simple interface for requesting information from the FuelPlanner.com API.

## Requirements

* PHP >= 5.4.x
* An account and license for the FuelPlanner.com website obtainable from [http://fuelplanner.com/api.php]()

## License

This client library is released under the [LICENSE](GPLv3), you are welcome to use it, improve it and contribute your changes back!

## Examples

Coming soon...

```php
<?php

<?php

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
echo 'Aircraft: ' . $response->airframe->name() . ' (' .$response->airframe->icao(). ')<br>';
echo 'Inital heading: ' . $response->initialHeading . '<br>';
echo 'Fuel usage (estimated): ' . number_format($response->estimatedFuelUsage->lbs()) . 'lbs / ' . number_format($response->estimatedFuelUsage->kgs(), 2) . ' kgs  / ' . number_format($response->estimatedFuelUsage->tonnes(), 5) . ' metric tonnes.<br>';
echo 'Gallons of fuel (estimated): ' . $response->estimatedFuelUsage->gallons() . ' gallons (' .$response->estimatedFuelUsage->litres(). ' litres).<br><br>';
echo 'Total flight distance: ' .$response->distance->nm(). ' nautical miles which is also converted to ' .$response->distance->km(). ' kilometers (' .$response->distance->mi(). ') and as meters ' .$response->distance->m(). ' (' .$response->distance->ft(). ' ft).';

```

## Support

I am happy to provide support via. my personal email address, so if you need a hand drop me an email at: [ballen@bobbyallen.me]().