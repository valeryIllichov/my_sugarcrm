<?php

/* yadl_spaceid - Skip Stamping */

// This script returns a JSON dataset of sales summary info
// "label1","amount1","label2","amount2"
require_once("FMPSales.php");
require_once("JSON.php");

header('Content-type: application/json');


strlen($_GET['startIndex']) > 0 ? $startIndex=$_GET['startIndex'] : $startIndex=0;
strlen($_GET['results']) > 0 ? $results=$_GET['results'] : $results=0;
strlen($_GET['sort']) > 0 ? $sort=$_GET['sort'] : $sort='custno';
strlen($_GET['dir']) > 0 ? $sort_dir=$_GET['dir'] : $sort_dir='asc';
strlen($_GET['select']) > 0 ? $selectMethod=$_GET['select'] : $selectMethod=null;
strlen($_GET['location']) > 0 ? $location=explode(';',$_GET['location']) : $location=null;
strlen($_GET['region']) > 0 ? $region=explode(';',$_GET['region']) : $region=null;
strlen($_GET['slsm']) > 0 ? $slsm=explode(";",$_GET['slsm']) : $slsm=null;
strlen($_GET['dealertype']) > 0 ? $dealertype=explode(';',$_GET['dealertype']) : $dealertype=null;
strlen($_GET['account']) > 0 ? $account=explode(';',$_GET['account']) : $account=null;

/* function getCustomerAR($startIndex, $maxRecords, $sort, $sort_dir, $location, $region, $slsm, $dealerType) { */
$returnArray=FMPSales::getCustomerTransactions($startIndex, $results, $sort, $sort_dir, $selectMethod, $location, $region, $slsm, $dealertype,$account,false,array());

$returnValue = array('recordsReturned'=>count($returnArray['data']),
        'totalRecords'=>$returnArray['totalRecords'],
        'startIndex'=>$startIndex,
        'sort'=>$sort,
        'dir'=>$sort_dir,
        'pageSize'=>$results,
		'records'=>$returnArray['data']
		);
$json = new Services_JSON();
echo $json->encode($returnValue);


// Return the data

function returnData($results, $startIndex, $sort, $dir, $sort_dir) {

    // Create return value
    $returnValue = array(
        'recordsReturned'=>count($data),
        'totalRecords'=>count($allRecords),
        'startIndex'=>$startIndex,
        'sort'=>$sort,
        'dir'=>$dir,
        'pageSize'=>$results,
        'records'=>$data
    );

    // JSONify
    //print json_encode($returnValue);

    // Use Services_JSON
    require_once('JSON.php');
    $json = new Services_JSON();
    echo ($json->encode($returnValue)); // Instead of json_encode
}

?>
