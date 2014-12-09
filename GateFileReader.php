<?php
/**
 * User: Robin Taylor
 * Date: 04/11/2014
 * Time: 11:42
 */

include_once('PatronDataReader.php');
//include_once('CoordinatesReader.php');

class GateFileReader {
    var $file;
    var $delimiter;

    var $hours_all;
    var $hours_main_gate;
    var $hours_cafe_gate;
    var $hours_hub_gate;

    var $hours_all_sce;
    var $hours_all_hss;
    var $hours_all_mvm;

    var $patronArray;
    var $postcodeArray;
    var $coordinatesArray;
    var $collegeArray;

    var $sce = 'College of Science and Engineering';
    var $hss = 'College of Humanities and Social Science';
    var $mvm = 'College of Medicine and Veterinary Medicine';

    var $entrantCategory;

    var $coordinatesString = "";

    function __construct($file, $delimiter) {
        $this->file = $file;
        $this->delimiter = $delimiter;

        // Zero fill hours arrays
        $this->hours_all = array();
        $this->hours_main_gate = array();
        $this->hours_cafe_gate = array();
        $this->hours_hub_gate = array();
        $this->hours_all_sce = array();
        $this->hours_all_hss = array();
        $this->hours_all_mvm = array();

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
        $patronReader = new PatronDataReader("/Users/rtaylor3/MainLibrary_TEST2_csv.csv",",");
        $this->patronArray = $patronReader->readFile();

        // Load up the coordinates array
        //$coordinatesReader = new CoordinatesReader();
        //$this->coordinatesArray = $coordinatesReader->readFile();


        $this->collegeArray = array();
        $this->collegeArray[$this->sce] = 0;
        $this->collegeArray[$this->hss] = 0;
        $this->collegeArray[$this->mvm] = 0;

        $this->entrantCategory = array();
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

            $coordinatesCounter = 0;

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
                    }
                    else {
                        $this->entrantCategory[$lineArray[4]] = 1;
                    }

                    // Group by postcode ***************

                    if (($patron->addressDesc == 'Temporary') && (substr($patron->zipCode, 0, 2) == 'EH')) {
                        //$postcodeFirstBit = $patron->zipCode;
                        $postcodeFirstBit = explode(" ", $patron->zipCode)[0];
                        if (array_key_exists($postcodeFirstBit, $this->postcodeArray)) {
                            $this->postcodeArray[$postcodeFirstBit]++;
                        } else {
                            $this->postcodeArray[$postcodeFirstBit] = 1;
                        }
                    }

                    // Sort the array highest to lowest value.
                    arsort($this->postcodeArray);

                    // Now get the matching coordinates

                    if ($coordinatesCounter < 20000) {
                        $coordinatesCounter++;

                        if (isset($patron->coordinate) && !empty($patron->coordinate)) {
                            $this->coordinatesString = $this->coordinatesString." new google.maps.LatLng(".$patron->coordinate->lat.", ".$patron->coordinate->lng."),";
                        }
                    }


                }
            }

            //print_r($this->coordinatesString);
            //print_r("Removing the last comma");
            // Remove the last comma
            $tempString = $this->coordinatesString;
            //rtrim($tempString,',');
            $tempString = substr($tempString,0,strlen($tempString)-1);

            $this->coordinatesString = substr($this->coordinatesString,0,strlen($this->coordinatesString)-1);


            //print_r("\n");
            //print_r("**********************************\n");

            //print_r($tempString);


            fclose($handle);
        } else {
            print_r("Error reading gate data file ".$this->file);
        }
    }


    function printArray($array) {
        foreach($array as $key => $value) {
            print_r("key is ".$key." ,value is ".$value."\n");
        }
    }

    function collegeEntrants() {

    }


}