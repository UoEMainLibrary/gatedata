<?php
/**
 * User: Robin Taylor
 * Date: 04/11/2014
 * Time: 11:42
 */

include_once('GateFileReader.php');

$fileReader = new GateFileReader("/Users/rtaylor3/MainLibrary.csv",",");

$fileReader->readFile();

//foreach($fileReader->hours as $hour) {
//    $jsonHours[] = array_values($hour);
//}


print_r("Total hours is \n");
printArray($fileReader->hours);
print_r("Main gate hours is \n");
printArray($fileReader->hours_main_gate);
print_r("Cafe gate hours is \n");
printArray($fileReader->hours_cafe_gate);
print_r("Hub gate hours is \n");
printArray($fileReader->hours_hub_gate);



function printArray($array) {
    foreach($array as $key => $value) {
        print_r("key is ".$key." ,value is ".$value."\n");
    }
}




