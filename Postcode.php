<?php
/**
 * User: Robin Taylor
 * Date: 05/12/2014
 * Time: 10:30
 */


$handle = @fopen("/Users/rtaylor3/projectsPhp/gatedata/postcodes", "r");
if ($handle) {
    while (($postcode = fgets($handle, 4096)) !== false) {
        getCoordinates(trim($postcode));
        // Wait for 1 seconds
        usleep(1000000);
    }
    if (!feof($handle)) {
        echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
}

//getCoordinates("EH10 4AN");

function getCoordinates($postcode) {
    $postcodeEncoded = urlencode($postcode);

    $json = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?components=postal_code:'.$postcodeEncoded.'|country:UK');

    $obj = json_decode($json);

    if ($obj->status == 'OK')
    {
        foreach ($obj->results as &$value) {

            print_r($postcode.", ".$value->geometry->location->lat.", ".$value->geometry->location->lng."\n");

        }
    //} else {
    //    print_r($postcode.", ".$obj->status.", ");
    }

}

