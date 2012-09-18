<?php

/* yadl_spaceid - Skip Stamping */

// This script returns a JSON dataset of sales summary info
// "label1","amount1","label2","amount2"
require_once("FMPSales.php");
require_once("JSON.php");

header('Content-type: application/json');

strlen($_GET['select']) > 0 ? $selectMethod=$_GET['select'] : $selectMethod=null;
strlen($_GET['location']) > 0 ? $location=explode(';',$_GET['location']) : $location=null;
strlen($_GET['region']) > 0 ? $region=explode(';',$_GET['region']) : $region=null;
strlen($_GET['slsm']) > 0 ? $slsm=explode(';',$_GET['slsm']) : $slsm=null;
strlen($_GET['dealertype']) > 0 ? $dealertype=explode(';',$_GET['dealertype']) : $dealertype=null;
strlen($_GET['account']) > 0 ? $account=explode(';',$_GET['account']) : $account=null;

$dataArray=FMPSales::getSalesTotals($selectMethod, $location, $region, $slsm, $dealertype,$account);


/* format: 4 columns listing label1, amount1, label2, amount2 */
$returnData = array();
foreach($dataArray as $row) { /* really only 1 */
	$returnData[] = array('label1'=>'Mtd Sales Projected', 'amount1'=>$row['MTD_PROJECTED'], 
			   'label2'=>'Ytd Sales Projected', 'amount2'=>$row['YTD_PROJECTED']);
	$returnData[] = array('label1'=>'Mtd Sales Invoiced', 'amount1'=>$row['MTD_SALES'], 
			   'label2'=>'Ytd Sales Invoiced', 'amount2'=>$row['YTD_SALES']);
	$returnData[] = array('label1'=>'Mtd GP $', 'amount1'=>$row['MTD_GP'], 
			   'label2'=>'Ytd GP $', 'amount2'=>$row['YTD_GP']);
	$returnData[] = array('label1'=>'Mtd GP %', 'amount1'=>$row['MTD_SALES']+0 != 0 ? round(($row['MTD_GP']/$row['MTD_SALES'])*100,2)."%" : '', 
			   'label2'=>'Ytd GP %', 'amount2'=> $row['YTD_SALES']+0 != 0 ? round(($row['YTD_GP']/$row['YTD_SALES'])*100,2).'%' : '');
	$returnData[] = array('label1'=>'Mtd Sales Budget', 'amount1'=>$row['MTD_BUDGET_SALES'], 
			   'label2'=>'Year Sales Budget', 'amount2'=>$row['YTD_BUDGET_SALES']);
	$returnData[] = array('label1'=>'Mtd GP$ Budget', 'amount1'=>$row['MTD_BUDGET_GP'], 
			   'label2'=>'Ytd GP$ Budget', 'amount2'=>$row['YTD_BUDGET_GP']);
	$returnData[] = array('label1'=>'Mtd GP % Budget', 'amount1'=>$row['MTD_BUDGET_SALES']+0 != 0 ? round(($row['MTD_BUDGET_GP']/$row['MTD_BUDGET_SALES'])*100,2).'%' : '', 
			   'label2'=>'Year GP % Budget', 'amount2'=>$row['YTD_BUDGET_SALES']+0 != 0 ? round(($row['YTD_BUDGET_GP']/$row['YTD_BUDGET_SALES'])*100,2).'%' : '');
	$returnData[] = array('label1'=>'Mly Sales Invoiced', 'amount1'=>$row['MLY_SLS'], 
			   'label2'=>'Lytd Sales Invoiced', 'amount2'=>$row['LYTD_SALES']);
	$returnData[] = array('label1'=>'Mtd Sales Total', 'amount1'=>$row['MTD_SALES'] + $row['PENDING_ORDERS'] + $row['PENDING_CREDITS'] + $row['TODAYS_ORDERS'] + $row['TODAYS_CREDITS'], 
			   'label2'=>'Ly Sales Invoiced', 'amount2'=>$row['LY_SALES']);
	$returnData[] = array('label1'=>'Pending Orders', 'amount1'=>$row['PENDING_ORDERS']+$row['TODAYS_ORDERS'], 
			   'label2'=>'Ly GP$', 'amount2'=>$row['LY_GP']);
	$returnData[] = array('label1'=>'Pending Credits', 'amount1'=>$row['PENDING_CREDITS']+ $row['TODAYS_CREDITS'], 
			   'label2'=>'Ly GP %', 'amount2'=>$row['LY_SALES']+0 != 0 ? round(($row['LY_GP'] / $row['LY_SALES']* 100),2).'%' : '');
	$returnData[] = array('label1'=>'&nbsp;', 'amount1'=>'&nbsp;', 
			   'label2'=>'&nbsp;', 'amount2'=>'&nbsp;');
	$returnData[] = array('label1'=>'Mtd Non-OE Sales', 'amount1'=>$row['MTD_SLS_NOEM'], 
			   'label2'=>'Ytd Non-OE Sales', 'amount2'=>$row['YTD_SLS_NOEM']);
	$returnData[] = array('label1'=>'Mtd Non-OE GP', 'amount1'=>$row['MTD_GP_NOEM'], 
			   'label2'=>'Ytd Non-OE GP', 'amount2'=>$row['YTD_GP_NOEM']);
	$returnData[] = array('label1'=>'Mtd Non-OE GP %', 'amount1'=>$row['MTD_SLS_NOEM']+0 != 0 ? round($row['MTD_GP_NOEM'] / $row['MTD_SLS_NOEM'] * 100,2).'%' : '', 
			   'label2'=>'Ytd Non-OE GP %', 'amount2'=>$row['YTD_SLS_NOEM']+0 != 0 ? round($row['YTD_GP_NOEM'] / $row['YTD_SLS_NOEM'] * 100,2).'%' : '');
	$returnData[] = array('label1'=>'Mtd Non-OE Bgt Sls', 'amount1'=>$row['MTD_BUDGET_NOEM_SALES'], 
			   'label2'=>'Ytd Non-OE Bgt-Sls', 'amount2'=>$row['YTD_BUDGET_NOEM_SALES']);
	$returnData[] = array('label1'=>'Mtd Non-OE Projected', 'amount1'=>$row['MTD_PROJECTED_NOEM'], 
			   'label2'=>'Ytd Non-OE Projected', 'amount2'=>$row['YTD_PROJECTED_NOEM']);
	$returnData[] = array('label1'=>'Ly Non-OE Sales', 'amount1'=>$row['LY_SLS_NOEM'], 
			   'label2'=>'Ly Non-OE GP %', 'amount2'=>$row['LY_SLS_NOEM']+0 != 0 ? round($row['LY_GP_NOEM']/$row['LY_SLS_NOEM']*100,2).'%' : '');
	$returnData[] = array('label1'=>'&nbsp;', 'amount1'=>'&nbsp;', 
			   'label2'=>'&nbsp;', 'amount2'=>'&nbsp;');
	$returnData[] = array('label1'=>'Mtd UnderCar Sales', 'amount1'=>$row['MTD_SLS_UNDERCAR'], 
			   'label2'=>'Ytd UnderCar Sales', 'amount2'=>$row['YTD_SLS_UNDERCAR']);
	$returnData[] = array('label1'=>'Mtd UnderCar GP', 'amount1'=>$row['MTD_GP_UNDERCAR'], 
			   'label2'=>'Ytd UnderCar GP', 'amount2'=>$row['YTD_GP_UNDERCAR']);
	$returnData[] = array('label1'=>'Mtd UnderCar GP %', 'amount1'=>$row['MTD_SLS_UNDERCAR']+0 > 0 ? round(($row['MTD_GP_UNDERCAR']/$row['MTD_SLS_UNDERCAR'])*100,2)."%" : '', 
			   'label2'=>'Ytd UnderCar GP %', 'amount2'=>$row['YTD_SLS_UNDERCAR']+0 > 0 ? round(($row['YTD_GP_UNDERCAR']/$row['YTD_SLS_UNDERCAR'])*100,2)."%" : '');
	$returnData[] = array('label1'=>'Mtd UnderCar Bgt Sls', 'amount1'=>$row['MTD_BUDGET_UNDERCAR_SALES'], 
			   'label2'=>'Ytd UnderCar Bgt Sls', 'amount2'=>$row['YTD_BUDGET_UNDERCAR_SALES']);
	$returnData[] = array('label1'=>'Mtd UnderCar Projected', 'amount1'=>$row['MTD_PROJECTED_UNDERCAR'], 
			   'label2'=>'Ytd UnderCar Projected', 'amount2'=>$row['YTD_PROJECTED_UNDERCAR']);
	$returnData[] = array('label1'=>'Ly UnderCar Sales', 'amount1'=>$row['LY_SLS_UNDERCAR'], 
			   'label2'=>'Ly UnderCar GP %', 'amount2'=>$row['LY_SLS_UNDERCAR']+0 > 0 ? round(($row['LY_GP_UNDERCAR']/$row['LY_SLS_UNDERCAR'])*100,2)."%" : '');

}


$returnValue = array('recordsReturned'=>count($returnData),
		'records'=>$returnData
		);
$json = new Services_JSON();
echo $json->encode($returnValue);


/*    $returnValue = array(
        'recordsReturned'=>count($data),
        'totalRecords'=>count($allRecords),
        'startIndex'=>$startIndex,
        'sort'=>$sort,
        'dir'=>$dir,
        'pageSize'=>$results,
        'records'=>$data
    );*/

?>
