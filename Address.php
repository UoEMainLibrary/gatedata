<?php
/**
 * User: Robin Taylor
 * Date: 16/12/2014
 * Time: 11:11
 */

class Address {

    var $addressDesc;
    var $addressType;
    var $addressLine1;
    var $addressLine2;
    var $addressLine3;
    var $addressLine4;
    var $addressLine5;
    var $city;
    var $stateProvince;
    var $zipCode;
    var $country;

    // The matching coordinates (object) retrieved from Google Maps API
    var $coordinate;

    function __construct() {

    }
}