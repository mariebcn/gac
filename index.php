<?php
ini_set('error_reporting', E_ALL);
ini_set("display_errors", 1);

$mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');

//Check POST params
if (empty($_FILES) || $_FILES['file']['name'] == '' || !(in_array($_FILES['file']['type'],$mimes))) 
    require 'view/viewImport.php';
else {
    //call controller
    require 'lib/mobileTraffic.php';
    $mobileTraffic = new MobileTraffic();
    
    if (isset($_FILES['file']['tmp_name']));
        $mobileTraffic->importAction($_FILES['file']['tmp_name']);
    
    $resultQueries = $mobileTraffic->resultAction();

    require 'view/viewResult.php';
}
?>