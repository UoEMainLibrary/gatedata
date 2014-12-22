<?php
/**
 * User: Robin Taylor
 * Date: 04/11/2014
 * Time: 11:42
 */

include_once('PatronDataReader.php');
//include_once('CoordinatesReader.php');

class GateFileReader {
    var $file = "/Users/rtaylor3/gatedata/MainLibrary.csv";
    var $delimiter = ",";

    var $hours_all = array();
    var $hours_main_gate = array();
    var $hours_cafe_gate = array();
    var $hours_hub_gate = array();

    var $hours_all_sce = array();
    var $hours_all_hss = array();
    var $hours_all_mvm = array();

    var $patronArray = array();
    var $postcodeArray = array();
    var $collegeArray = array();

    var $sce = 'College of Science and Engineering';
    var $hss = 'College of Humanities and Social Science';
    var $mvm = 'College of Medicine and Veterinary Medicine';

    var $entrantCategory  = array();

    var $coordinatesCounter = 0;
    var $coordinatesString = "";

    function __construct() {
        // Zero fill hours arrays
        for ($i=0; $i < 24; $i++) {
            $this->hours_all[$i] = 0;
            $this->hours_main_gate[$i] = 0;
            $this->hours_cafe_gate[$i] = 0;
            $this->hours_hub_gate[$i] = 0;
            $this->hours_all_sce[$i] = 0;
            $this->hours_all_hss[$i] = 0;
            $this->hours_all_mvm[$i] = 0;
        }

        // Load up the Patron data array
        $patronReader = new PatronDataReader("/Users/rtaylor3/gatedata/MainLibrary_TEST2_csv.csv",",");
        $this->patronArray = $patronReader->readFile();

        $this->collegeArray[$this->sce] = 0;
        $this->collegeArray[$this->hss] = 0;
        $this->collegeArray[$this->mvm] = 0;

        $this->entrantCategory['VIS'] = 0;
        $this->entrantCategory['STF'] = 0;
        $this->entrantCategory['UGN'] = 0;
        $this->entrantCategory['UGR'] = 0;
        $this->entrantCategory['PGN'] = 0;
        $this->entrantCategory['PGR'] = 0;
        $this->entrantCategory['NGN'] = 0;
        $this->entrantCategory['PGE'] = 0;
        $this->entrantCategory['PTM'] = 0;
    }

    function readFile() {
        if (($handle = fopen($this->file, "r")) !== FALSE) {

            //$coordinatesCounter = 0;

            while (($lineArray = fgetcsv($handle, 4000, $this->delimiter)) !== FALSE) {

                // First get the matching patron data ***************

                if (array_key_exists($lineArray[3], $this->patronArray)) {
                    $patron = $this->patronArray[$lineArray[3]];

                    // Group by entry time ************

                    if (substr($lineArray[1], 0, 2) == '00') {
                        $hour = '0';
                    } else {
                        $hour = ltrim(substr($lineArray[1], 0, 2), '0');
                    }

                    if (($lineArray[6] == 'U128/04') ||
                        ($lineArray[6] == 'U128/05') ||
                        ($lineArray[6] == 'U128/06') ||
                        ($lineArray[6] == 'U128/07')) {
                        $this->hours_main_gate[$hour]++;
                        $this->hours_all[$hour]++;
                    } elseif ($lineArray[6] == 'U128/09') {
                        $this->hours_cafe_gate[$hour]++;
                        $this->hours_all[$hour]++;
                    } elseif ($lineArray[6] == 'U127/11') {
                        $this->hours_hub_gate[$hour]++;
                    }

                    // Group by college ***************

                    if (strstr($patron->note, $this->sce)) {
                        $this->collegeArray[$this->sce]++;
                        $this->hours_all_sce[$hour]++;
                    } elseif (strstr($patron->note, $this->hss)) {
                        $this->collegeArray[$this->hss]++;
                        $this->hours_all_hss[$hour]++;
                    } elseif (strstr($patron->note, $this->mvm)) {
                        $this->collegeArray[$this->mvm]++;
                        $this->hours_all_mvm[$hour]++;
                    }

                    // Group by entrant category ***********

                    if (array_key_exists($lineArray[4], $this->entrantCategory)) {
                        $this->entrantCategory[$lineArray[4]]++;
                        //print_r($lineArray[3]);
                    } else {
                        //print_r($lineArray[3]);
                        $this->entrantCategory[$lineArray[4]] = 1;
                    }

                    // Group by postcode ***************

                    //print_r($patron->tempAddress);
                    //print_r($patron->permAddress);

                    if ((isset($patron->tempAddress) && (!empty($patron->tempAddress)))) {

                        //print_r("Using temp address \n");
                        if (substr($patron->tempAddress->zipCode, 0, 2) == 'EH') {
                            $postcodeFirstBit = explode(" ", $patron->tempAddress->zipCode)[0];
                            if (array_key_exists($postcodeFirstBit, $this->postcodeArray)) {
                                $this->postcodeArray[$postcodeFirstBit]++;
                            } else {
                                $this->postcodeArray[$postcodeFirstBit] = 1;
                            }

                            if (isset($patron->tempAddress->coordinate) && !empty($patron->tempAddress->coordinate)) {
                                $this->makeCoordinateString($patron->tempAddress->coordinate);
                            }
                        }
                    } elseif ((isset($patron->permAddress)) && (!empty($patron->permAddress))) {

                        //print_r("Using perm address \n");
                        if (substr($patron->permAddress->zipCode, 0, 2) == 'EH') {
                            $postcodeFirstBit = explode(" ", $patron->permAddress->zipCode)[0];
                            if (array_key_exists($postcodeFirstBit, $this->postcodeArray)) {
                                $this->postcodeArray[$postcodeFirstBit]++;
                            } else {
                                $this->postcodeArray[$postcodeFirstBit] = 1;
                            }

                            if (isset($patron->permAddress->coordinate) && !empty($patron->permAddress->coordinate)) {
                                $this->makeCoordinateString($patron->permAddress->coordinate);
                            }
                        }


                    }
                }
            }

            // Sort the postcode array highest to lowest value.
            arsort($this->postcodeArray);

            // Remove the last comma from the coordinates String.
            $this->coordinatesString = substr($this->coordinatesString,0, strlen($this->coordinatesString) - 1);

            fclose($handle);
        } else {
            print_r("Error reading gate data file ".$this->file);
        }
    }

    function makeCoordinateString($coordinate) {
        if ($this->coordinatesCounter < 20000) {
            $this->coordinatesCounter++;

            $this->coordinatesString = $this->coordinatesString." new google.maps.LatLng(".$coordinate->lat.", ".$coordinate->lng."),";
        }
    }


    function printArray($array) {
        foreach($array as $key => $value) {
            print_r("key is ".$key." ,value is ".$value."\n");
        }
    }


}