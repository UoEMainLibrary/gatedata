<?php
/**
 * User: Robin Taylor
 * Date: 05/12/2014
 * Time: 16:52
 */

class CoordinatesReader {

    function readFile() {
        if (($handle = fopen("/Users/rtaylor3/projectsPhp/gatedata/coordinates", "r")) !== FALSE) {

            while (($lineArray = fgetcsv($handle, 0, ",")) !== FALSE) {
                if (!empty($lineArray[1])) {


                        $coordinate = new Coordinate;

                        //print_r(" "."\r\n");
                        //print_r("New Rec "."\r\n");
                        //print_r($lineArray[0]."\r\n");

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

        //print_r(count($this->coordinateArray));

        return $this->coordinateArray;
   }
}