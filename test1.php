<?php
/**
 * User: Robin Taylor
 * Date: 04/11/2014
 * Time: 11:42
 */

include_once('PatronDataReader.php');

$patronReader = new PatronDataReader("/Users/rtaylor3/MainLibrary_TEST2_csv.csv",",");
$patronReader->readFile();


function printArray($array) {
    foreach($array as $key => $value) {
        print_r("key is ".$key." ,value is ".$value."\n");
    }
}




