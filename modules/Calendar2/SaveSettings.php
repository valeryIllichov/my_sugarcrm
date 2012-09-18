<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point'); 
/******************************************************************************
OpensourceCRM End User License Agreement

INSTALLING OR USING THE OpensourceCRM's SOFTWARE THAT YOU HAVE SELECTED TO 
PURCHASE IN THE ORDERING PROCESS (THE "SOFTWARE"), YOU ARE AGREEING ON BEHALF OF
THE ENTITY LICENSING THE SOFTWARE ("COMPANY") THAT COMPANY WILL BE BOUND BY AND 
IS BECOMING A PARTY TO THIS END USER LICENSE AGREEMENT ("AGREEMENT") AND THAT 
YOU HAVE THE AUTHORITY TO BIND COMPANY.

IF COMPANY DOES NOT AGREE TO ALL OF THE TERMS OF THIS AGREEMENT, DO NOT SELECT 
THE "ACCEPT" BOX AND DO NOT INSTALL THE SOFTWARE. THE SOFTWARE IS PROTECTED BY 
COPYRIGHT LAWS AND INTERNATIONAL COPYRIGHT TREATIES, AS WELL AS OTHER 
INTELLECTUAL PROPERTY LAWS AND TREATIES. THE SOFTWARE IS LICENSED, NOT SOLD.

    *The COMPANY may not copy, deliver, distribute the SOFTWARE without written
     permit from OpensourceCRM.
    *The COMPANY may not reverse engineer, decompile, or disassemble the 
    SOFTWARE, except and only to the extent that such activity is expressly 
    permitted by applicable law notwithstanding this limitation.
    *The COMPANY may not sell, rent, or lease resell, or otherwise transfer for
     value, the SOFTWARE.
    *Termination. Without prejudice to any other rights, OpensourceCRM may 
    terminate this Agreement if the COMPANY fail to comply with the terms and 
    conditions of this Agreement. In such event, the COMPANY must destroy all 
    copies of the SOFTWARE and all of its component parts.
    *OpensourceCRM will give the COMPANY notice and 30 days to correct above 
    before the contract will be terminated.

The SOFTWARE is protected by copyright and other intellectual property laws and 
treaties. OpensourceCRM owns the title, copyright, and other intellectual 
property rights in the SOFTWARE.
*****************************************************************************/
global $current_user;
global $timedate;


function to_db_time($hours,$minutes,$mer){
	$hours = intval($hours);
	$minutes = intval($minutes);
	$mer = strtolower($mer);
	if(!empty($mer)){
		if(($mer) == 'am')
			if($hours == 12)
				$hours = $hours - 12;
		if(($mer) == 'pm')
			if($hours != 12)
				$hours = $hours + 12;		
	}
	if($hours < 10)
		$hours = "0".$hours;
	if($minutes < 10)
		$minutes = "0".$minutes;	
	return $hours . ":". $minutes; 
}


if (isset($_REQUEST['d_start_meridiem'])){
	$db_start = to_db_time($_REQUEST['d_start_hours'],$_REQUEST['d_start_minutes'],$_REQUEST['d_start_meridiem']);
	$db_end = to_db_time($_REQUEST['d_end_hours'],$_REQUEST['d_end_minutes'],$_REQUEST['d_end_meridiem']);
} else {
	$db_start = to_db_time($_REQUEST['d_start_hours'],$_REQUEST['d_start_minutes']);
	$db_end = to_db_time($_REQUEST['d_end_hours'],$_REQUEST['d_end_minutes']);
}

$current_user->setPreference('d_start_time', $db_start, 0, 'global', $current_user);
$current_user->setPreference('d_end_time', $db_end, 0, 'global', $current_user);
//$current_user->setPreference('d_start_meridiem', $_REQUEST['d_start_meridiem'], 0, 'global', $current_user);
//$current_user->setPreference('d_end_meridiem', $_REQUEST['d_end_meridiem'], 0, 'global', $current_user);

$current_user->setPreference('week_start_day', $_REQUEST['start_day'], 0, 'global', $current_user);
$current_user->setPreference('show_tasks', $_REQUEST['show_tasks'], 0, 'global', $current_user);
$current_user->setPreference('auto_accept', $_REQUEST['auto_accept'], 0, 'global', $current_user);

if(isset($_REQUEST['current_module']) && !empty($_REQUEST['current_module'])) {
	$current_module = $_REQUEST['current_module'];
} else {
	global $currentModule;
	$current_module = $currentModule;
}
header("Location: index.php?module=".$current_module."&action=index&view=".$_REQUEST['view']);
?>
