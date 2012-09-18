<?php
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
/**
 *Calendar2ViewAjaxFlyCreate
 * 
 * */
 
require_once('include/MVC/View/SugarView.php');
require_once('modules/Calendar2/Calendar2.php');
require_once("modules/Calendar2/functions.php");

class Calendar2ViewAjaxFlyCreate extends SugarView {
	
 	function Calendar2ViewAjaxFlyCreate(){
 		parent::SugarView();
 	}
 	
 	function process() {
		$this->display();
 	}

 	function display() {

		require_once("modules/Calls/Call.php");
		require_once("modules/Meetings/Meeting.php");
		
		require_once("modules/Calendar2/functions.php");

		if($_REQUEST['type'] == 'call'){
			$bean = new Call();
			$type = 'call';
			$jn = "cal2_call_id_c";	
		}
		if($_REQUEST['type'] == 'meeting'){
			$bean = new Meeting();
			$type = 'meeting';
			$jn = "cal2_meeting_id_c";
		}
			
//		if(!$bean->ACLAccess('Save')) {
//			$json_arr = array(
//				'succuss' => 'no',
//			);
//			echo json_encode($json_arr);
//			die;
//		}

		$bean->duration_hours = $_REQUEST['duration_hours'];
		$bean->duration_minutes = $_REQUEST['duration_minutes'];
		$bean->assigned_user_id = $GLOBALS['current_user']->id;
		
		$bean->name = $_REQUEST['title'];
		if (isset($_REQUEST['location']) && !empty($_REQUEST['location']) && isset($bean->location)) $bean->location = $_REQUEST['location'];
		$bean->reminder_time = -1;

		$bean->date_start = isset($_REQUEST['datetime'])? $_REQUEST['datetime'] : "";
		$bean->date_end = isset($_REQUEST['date_end'])? $_REQUEST['date_end'] : "";

		$bean->update_vcal = false;
		$bean->save();
		
		$bean->retrieve($bean->id);
		
		if (isset($_REQUEST['contact_id']) && !empty($_REQUEST['contact_id'])) {
			$bean->load_relationship('contacts');
			$bean->contacts->add($_REQUEST['contact_id']);
		}

		if (isset($_REQUEST['account_id']) && !empty($_REQUEST['account_id'])) {
			$bean->load_relationship('accounts');
			$bean->accounts->add($_REQUEST['account_id']);
		}
		
		global $timedate;
		$date_start = to_db($bean->date_start);
		$date_unix = to_timestamp($date_start);	
		$start = $date_unix;

		if($type == 'call') $users = $bean->get_call_users();
		if($type == 'meeting') $users = $bean->get_meeting_users();
		$user_ids = array();
		foreach($users as $u) {
			$user_ids[] = $u->id;
			vCal2::cache_sugar_vcal($u);
		}
		$team_id = "";
		$team_name = "";
		if (isPro()) {
			$team_id = $bean->team_id;
			$team_name = $bean->team_name;
		} else {
			$team_id = "";
			$team_name = "";
		}

		$loc = (!is_null($bean->location)) ? $bean->location : "";

		//shorten time_start for dashlet
		if ($_REQUEST['currentmodule'] == "Home") {
			//$temp_time_start = timestamp_to_user_formated($start + $GLOBALS['timedate']->get_hour_offset() * 3600, $GLOBALS['timedate']->get_time_format(false));
			$temp_time_start = date($GLOBALS['timedate']->get_time_format(false), $start + $GLOBALS['timedate']->get_hour_offset() * 3600);
		} else {
			//$temp_time_start = timestamp_to_user_formated($start + $GLOBALS['timedate']->get_hour_offset() * 3600, $GLOBALS['timedate']->get_time_format());
			$temp_time_start = date($GLOBALS['timedate']->get_time_format(), $start + $GLOBALS['timedate']->get_hour_offset() * 3600);
		}

		$arr_rec = array();
		
		$start = to_timestamp_from_uf($bean->date_start);
		
		$json_arr = array(
			'succuss' => 'yes',
			'record_name' => $bean->name,
			'record' => $bean->id,
			'type' => $type,
			'assigned_user_id' => $bean->assigned_user_id,
			'user_id' => '',
			'user_name' => $bean->assigned_user_name,
			'date_start' => $bean->date_start,
			'start' => $start,
			'time_start' => timestamp_to_user_formated2($start,$GLOBALS['timedate']->get_time_format()),
			'duration_hours' => $bean->duration_hours,
			'duration_minutes' => $bean->duration_minutes,
			'description' => $bean->description,
			'status' => $bean->status,
			'location' => $loc,
			'team_id' => $team_id,
			'team_name' => $team_name,
			'users' => $user_ids,
			'cal2_recur_id_c' => $bean->$jn,
			'cal2_repeat_type_c' => $bean->cal2_repeat_type_c,
			'cal2_repeat_interval_c' => $bean->cal2_repeat_interval_c,
			'cal2_repeat_end_date_c' => $bean->cal2_repeat_end_date_c,
			'cal2_repeat_days_c' => $bean->cal2_repeat_days_c,
			'arr_rec' => $arr_rec,
			'detailview' => 1,
		);
		echo json_encode($json_arr);
		

	}
}
?>
