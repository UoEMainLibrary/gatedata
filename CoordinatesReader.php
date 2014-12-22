<?php
/**
 * User: Robin Taylor
 * Date: 05/12/2014
 * Time: 16:52
 */

class CoordinatesReader {
    var $file = "/Users/rtaylor3/projectsPhp/gatedata/coordinates";
    var $delimiter = ",";

    var $coordinateArray;

    function __construct() {
        $this->coordinateArray = array();
    }

    function readFile() {
        if (($handle = fopen($this->file, "r")) !== FALSE) {

            while (($lineArray = fgetcsv($handle, 0, $this->delimiter)) !== FALSE) {

                if (!empty($lineArray[1])) {
                    $coordinate = new Coordinate;

                    $coordinate->postCode = $lineArray[0];
                    $coordinate->lat = $lineArray[1];
                    $coordinate->lng = $lineArray[2];

                    $this->coordinateArray[$lineArray[0]] = $coordinate;
                }
            }

            fclose($handle);
        } else {
            print_r("Error reading coordinate data file ".$this->file);
        }

        return $this->coordinateArray;
   }
}