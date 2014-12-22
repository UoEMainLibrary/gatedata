<?php
/**
 * User: Robin Taylor
 * Date: 20/11/2014
 * Time: 11:28
 */

class Patron {

    var $id;
    var $patronGroup;
    var $barCode;
    var $barCodeStatus;

    // Permanent address (object)
    var $permAddress;
    // Temporary address (object)
    var $tempAddress;

    var $noteType;
    var $note;
    var $modifyOperatorId;


    function __construct() {

    }


}