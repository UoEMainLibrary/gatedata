<?php
/**
 * User: Robin Taylor
 * Date: 20/11/2014
 * Time: 11:28
 */

include_once('Patron.php');
include_once('CoordinatesReader.php');
include_once('Coordinate.php');

class PatronDataReader {
    var $file;
    var $delimiter;
    var $patronArray;
    var $coordinatesArray;

    function __construct($file, $delimiter) {
        $this->file = $file;
        $this->delimiter = $delimiter;
        $this->patronArray = array();

        // Load up the coordinates array
        $coordinatesReader = new CoordinatesReader();
        $this->coordinatesArray = $coordinatesReader->readFile();

    }

    function readFile() {
        if (($handle = fopen($this->file, "r")) !== FALSE) {

            while (($lineArray = fgetcsv($handle, 0, $this->delimiter)) !== FALSE) {
                if (!empty($lineArray[1])) {

                    // Just save the temporary, not permanent address, and ignore multiple addresses for same person.
                    if (($lineArray[4] == 'Temporary') && (!array_key_exists($lineArray[3], $this->patronArray))){

                        $patron = new Patron;

                        //print_r(" "."\r\n");
                        //print_r("New Rec "."\r\n");
                        //print_r($lineArray[0]."\r\n");

                        $patron->id = $lineArray[0];
                        $patron->patronGroup = $lineArray[1];
                        $patron->barCode = $lineArray[2];
                        $patron->barCodeStatus = $lineArray[3];
                        $patron->addressDesc = $lineArray[4];
                        $patron->addressType = $lineArray[5];
                        $patron->addressLine1 = $lineArray[6];
                        $patron->addressLine2 = $lineArray[7];
                        $patron->addressLine3 = $lineArray[8];
                        $patron->addressLine4 = $lineArray[9];
                        $patron->addressLine5 = $lineArray[10];
                        $patron->city = $lineArray[11];
                        $patron->stateProvince = $lineArray[12];
                        $patron->zipCode = $lineArray[13];
                        $patron->country = $lineArray[14];
                        $patron->noteType = $lineArray[15];
                        $patron->note = $lineArray[16];
                        $patron->modifyOperatorId = $lineArray[17];

                        // Now get the matching coordinates
                        if (array_key_exists($lineArray[13], $this->coordinatesArray)) {
                            //print_r("Found matching coordinates");
                            $patron->coordinate = $this->coordinatesArray[$lineArray[13]];
                        }

                        $this->patronArray[$lineArray[0]] = $patron;
                    }

                }
            }

            fclose($handle);
        } else {
            print_r("Error reading patron data file ".$this->file);
        }

        return $this->patronArray;

    }

}