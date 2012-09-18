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

function t_to_date($curr_time){
	return timestamp_to_user_formated2($curr_time,'Y')."-".timestamp_to_user_formated($curr_time,'m')."-".timestamp_to_user_formated($curr_time,'d');
}

function timestamp_to_user_formated($t,$format = false){
	global $timedate;
	if($format == false)
		$f = $timedate->get_date_time_format();
	else
		$f = $format;		
	
	return date($f,$t + $timedate->get_hour_offset() * 3600);	
}

function timestamp_to_user_formated2($t,$format = false){
	global $timedate;
	if($format == false)
		$f = $timedate->get_date_time_format();
	else
		$f = $format;		
		
	return date($f,$t - date('Z',$t) );	
}


function to_db($d){
	global $timedate;
	global $current_user;
	
	$db_d = $timedate->to_db($d);
	
	if( !($timedate->inDST($db_d, $timedate->getUserTimeZone($current_user))) ){
		$date_unix = to_timestamp($db_d);
		$date_unix = $date_unix - date('I',$date_unix - date('Z',$date_unix))*3600*2;
		$db_d = date($timedate->get_db_date_time_format(), $date_unix - date('Z',$date_unix));	
		//echo " -- " . date('I',$date_unix - date('Z'))	;		
	}	
			
	return $db_d;	
}

function to_db2($d){
	global $timedate;
	global $current_user;
	
	$db_d = $timedate->to_db($d);

	$timezone = $GLOBALS['timedate']->getUserTimeZone();
	$gmtOffset = $timezone['gmtOffset'];
	$ts_d = to_timestamp($db_d) + $gmtOffset * 60;
	
	return date("Y-d-m H:i:s",$ts_d - date('Z',$ts_d));
	
}

function to_timestamp_from_uf($d){
	global $timedate;
	global $current_user;
	
	$db_d = $timedate->to_db($d);



	$timezone = $GLOBALS['timedate']->getUserTimeZone();
	$gmtOffset = $timezone['gmtOffset'];
	$ts_d = to_timestamp($db_d) + $gmtOffset * 60;
	
	if($timedate->inDST($db_d,$timedate->getUserTimeZone()))
		$ts_d += 3600;
	
	return $ts_d;	
}

function to_timestamp($db_d){
	$date_parsed = date_parse($db_d);
	$date_unix = gmmktime($date_parsed['hour'],$date_parsed['minute'],$date_parsed['second'],$date_parsed['month'],$date_parsed['day'],$date_parsed['year']);
	
	return $date_unix;
}


function getDST($t){
	$t = intval($t);
	$timezone = $GLOBALS['timedate']->getUserTimeZone();
	$gmtOffset = $timezone['gmtOffset'];	
	return date("I",$t - date('Z',$t) + $gmtOffset * 60 );
}


function get_invitees_list($bean,$type){
			$userInvitees = array();
			$q = 'SELECT mu.user_id, mu.accept_status FROM '.$type.'s_users mu WHERE mu.'.$type.'_id = \''.$bean->id.'\' AND mu.deleted = 0 ';
			$r = $bean->db->query($q);
			while($a = $bean->db->fetchByAssoc($r))
				$userInvitees[] = $a['user_id'];			
					
			return $userInvitees;					
}

function add_zero($t){
	if($t < 10)
		return "0" . $t;
	else
		return $t;
}

function is551() {
	global $sugar_version;
	if($sugar_version < '5.5') {
		return false;
	} else {
		return true;
	}
}

function isPro() {
	global $sugar_flavor;
	if ($sugar_flavor == "ENT" || $sugar_flavor == "PRO") {
		return true;
	} else {
		return false;
	}
}
?>
