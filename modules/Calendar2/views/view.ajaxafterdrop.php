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
 *Calendar2ViewAjaxAfterDrop
 * 
 * */
 
require_once('include/MVC/View/SugarView.php');
require_once('modules/Calendar2/Calendar2.php');

class Calendar2ViewAjaxAfterDrop extends SugarView {
	
 	function Calendar2ViewAjaxAfterDrop(){
 		parent::SugarView();
 	}
 	
 	function process() {
		$this->display();
 	}

 	function display() {

		require_once("modules/Calls/Call.php");
		require_once("modules/Meetings/Meeting.php");

		if($_REQUEST['type'] == 'call')
			$bean = new Call();
		if($_REQUEST['type'] == 'meeting')
			$bean = new Meeting();
			
		$bean->retrieve($_REQUEST['record']);
		
//		if(!$bean->ACLAccess('Save')){
//			die;
//		}

		$bean->date_start = isset($_REQUEST['datetime'])? $_REQUEST['datetime'] : "";
		//$bean->date_start = $_REQUEST['datetime'];
		$bean->date_end = isset($_REQUEST['date_end'])? $_REQUEST['date_end'] : "";
		//$bean->date_end = $_REQUEST['date_end'];

		//vCal is updated later
		$bean->update_vcal = false;
		$bean->save();

		//updating vCal
		$userlist = array();
		if($_REQUEST['type'] == 'call') {
			$userlist = $bean->get_call_users();
		} else {
			$userlist = $bean->get_meeting_users();
		}
		$user_ids = array();
		foreach($userlist as $u) {
			$user_ids[] = $u->id;
			vCal2::cache_sugar_vcal($u);
		}

		require_once('modules/Resources/Resource.php');
		$bean->load_relationship('resources');
		$reslist = array();
		$reslist = $bean->resources->get(false);
		foreach($reslist as $r) {
			$res = new Resource();
			$res->retrieve($r);
			$user_ids[] = $res->id;
			vCal2::cache_sugar_vcal($res);
		}

		$json_arr = array(
			'succuss' => 'yes',
			'users' => $user_ids,
		);
		echo json_encode($json_arr);
	}
}
?>
