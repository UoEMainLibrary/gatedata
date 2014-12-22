<?php
/**
 * User: Robin Taylor
 * Date: 20/11/2014
 * Time: 11:28
 */

include_once('Patron.php');
include_once('Address.php');
include_once('CoordinatesReader.php');
include_once('Coordinate.php');

class PatronDataReader {
    var $file = "/Users/rtaylor3/gatedata/MainLibrary_TEST2_csv.csv";
    var $delimiter = ",";
    var $patronArray = array();
    var $coordinatesArray = array();

    function __construct() {
        // Load up the coordinates array
        $coordinatesReader = new CoordinatesReader();
        $this->coordinatesArray = $coordinatesReader->readFile();
    }

    function readFile() {
        if (($handle = fopen($this->file, "r")) !== FALSE) {

            while (($lineArray = fgetcsv($handle, 0, $this->delimiter)) !== FALSE) {
                if (!empty($lineArray[0])) {

                    if (!array_key_exists($lineArray[0], $this->patronArray)){

                        // New patron
                        $patron = new Patron;

                        $patron->id = $lineArray[0];
                        $patron->patronGroup = $lineArray[1];
                        $patron->barCode = $lineArray[2];
                        $patron->barCodeStatus = $lineArray[3];

                        if ($lineArray[4] == 'Permanent') {
                            $patron->permAddress = $this->addAddress($lineArray);
                        } elseif ($lineArray[4] == 'Temporary') {
                            $patron->tempAddress = $this->addAddress($lineArray);
                        }

                        $patron->noteType = $lineArray[15];
                        $patron->note = $lineArray[16];
                        $patron->modifyOperatorId = $lineArray[17];

                        $this->patronArray[$lineArray[0]] = $patron;

                    } else {

                        // We already have a patron rec, just add any missing addresses
                        $patron = $this->patronArray[$lineArray[0]];

                        // Note - there may be multiple perm and temp addresses, but we are only going to store one of each
                        if (($lineArray[4] == 'Permanent') && (!isset($patron->permAddress)) && (empty($patron->permAddress))) {
                            $patron->permAddress = $this->addAddress($lineArray);
                        } elseif (($lineArray[4] == 'Temporary') && (!isset($patron->tempAddress)) && (empty($patron->tempAddress))) {
                            $patron->tempAddress = $this->addAddress($lineArray);
                        }
                    }
                }
            }

            fclose($handle);
        } else {
            print_r("Error reading patron data file ".$this->file);
        }

        return $this->patronArray;
    }

    function addAddress($lineArray) {
        $address = new Address;

        $address->addressDesc = $lineArray[4];
        $address->addressType = $lineArray[5];
        $address->addressLine1 = $lineArray[6];
        $address->addressLine2 = $lineArray[7];
        $address->addressLine3 = $lineArray[8];
        $address->addressLine4 = $lineArray[9];
        $address->addressLine5 = $lineArray[10];
        $address->city = $lineArray[11];
        $address->stateProvince = $lineArray[12];
        $address->zipCode = $lineArray[13];
        $address->country = $lineArray[14];

        // If we have a postcode then look up the coordinates
        if (array_key_exists($lineArray[13], $this->coordinatesArray)) {
            $address->coordinate = $this->coordinatesArray[$lineArray[13]];
        }

        return $address;
    }


}