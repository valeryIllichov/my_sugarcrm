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
 *Calendar2ViewAjaxLoad
 * 
 * */
 
require_once('include/MVC/View/SugarView.php');
require_once("modules/Calendar2/functions.php");

class Calendar2ViewAjaxLoad extends SugarView {
	
 	function Calendar2ViewAjaxLoad(){
 		parent::SugarView();
 	}
 	
 	function process() {
		$this->display();
 	}

 	function display() {
		require_once("modules/Calls/Call.php");
		require_once("modules/Meetings/Meeting.php");

		global $beanFiles,$beanList,$db;

		if($_REQUEST['cur_module'] == 'Calls'){
			$bean = new Call();
			$type = 'call';
			$table_name = $bean->table_name;
			$jn = "cal2_call_id_c";		
		}
		if($_REQUEST['cur_module'] == 'Meetings'){
			$bean = new Meeting();
			$type = 'meeting';
			$table_name = $bean->table_name;
			$jn = "cal2_meeting_id_c";
		}

		$json_arr = array();

		if ($bean->retrieve($_REQUEST['record'])) {

//			if(!$bean->ACLAccess('DetailView')) {
//				$json_arr = array(
//					'succuss' => 'no',
//				);
//				echo json_encode($json_arr);
//				die;
//			}

		//	$bean->retrieve($r_id); 
                        $lead_account_name = '';
			if(!empty($bean->parent_type) && !empty($bean->parent_id)){
				require_once($beanFiles[$beanList[$bean->parent_type]]);
				$par = new $beanList[$bean->parent_type]();
				$par->retrieve($bean->parent_id);
                                if(strtolower($bean->parent_type) == "leads") $lead_account_name = $par->account_name;
			}

			global $timedate, $currentModule, $sugar_version;
                        if (!$timedate) {
                                $timedate = new TimeDate();
                        }
                        
			$date_start = $timedate->to_db($bean->date_start);
			$date_parsed = date_parse($date_start);
			$date_unix = gmmktime($date_parsed['hour'],$date_parsed['minute'],$date_parsed['second'],$date_parsed['month'],$date_parsed['day'],$date_parsed['year']);
			$start = $date_unix;
                        //$GLOBALS['log']->test("In ajaxload bean->date_start  ".$bean->date_start);
                        //$GLOBALS['log']->test("In ajaxload date_start  ".$date_start);
                        //$GLOBALS['log']->test("In ajaxload date_parsed  ".$date_parsed);
                        //$GLOBALS['log']->test("In ajaxload date_unix  ".$date_unix);
					

                        // Previous outcome
                        $previous_outcome ='';
                        $previous_date ='';
                        $previous_status = '';
                        $next_description = '';
                        $next_date = '';
                        
                        $timezone = $timedate->getUserTimeZone();
                        //$start_for_previous = strtotime($bean->date_start)-$timezone['gmtOffset']*60;
                        $start_for_previous = strtotime($date_start);
                        $start_for_next = strtotime($date_start);
                        //$GLOBALS['log']->test("In ajaxload bean->date_start  ".$bean->date_start);
                        //$GLOBALS['log']->test("In ajaxload start_for_previous  ".$start_for_previous);
                        

                        if(!empty($bean->parent_type) && !empty($bean->parent_id)){
				if($bean->parent_type == 'Accounts' || $bean->parent_type == 'Leads' )
                                {

                                    $q = ''
                                           . 'SELECT '
                                           . 'outcome_c, date_start, status  '
                                           . 'FROM ' . $table_name . ' '
                                           . 'WHERE parent_id = \'' . $bean->parent_id . '\' '
                                           . ' AND UNIX_TIMESTAMP(date_start) < \''.$start_for_previous.'\''
                                           . ' AND deleted=0 '
                                           . 'ORDER BY UNIX_TIMESTAMP(date_start)DESC LIMIT 1';
                                            $rs = $db->query($q);

                               while($row = $db->fetchByAssoc($rs)) {
                                    $previous_outcome = $row['outcome_c'];
                                    $previous_date = $timedate->to_display_date_time($row['date_start']);
                                    $previous_status = $row['status'];
                               }
                                    $q_next = ''
                                           . 'SELECT '
                                           . 'date_start, description  '
                                           . 'FROM ' . $table_name . ' '
                                           . 'WHERE parent_id = \'' . $bean->parent_id . '\' '
                                           . ' AND UNIX_TIMESTAMP(date_start) > \''.$start_for_next.'\''
                                           . ' AND deleted=0 '
                                           . 'ORDER BY UNIX_TIMESTAMP(date_start) ASC LIMIT 1';
                                            $rs_next = $db->query($q_next);
                              while($row = $db->fetchByAssoc($rs_next)) {
                                    $next_description = $row['description'];
                                    $next_date = $timedate->to_display_date_time($row['date_start']);

                               }
                              }
			}
                        $opportunities_arr = array();
                        $sales_stage_arr = array('Stage1' => 'Initial Contact', 'Stage 2' => 'Qualifying Questions', 'Stage 3' => 'Presentation of Product', 'Stage 4 ' => 'Draft Proposal', 'Stage 5' => 'Final Proposal');


                        //getting oppotrunities list for account parant type
                        if(!empty($bean->parent_type) && !empty($bean->parent_id)){
				if($bean->parent_type == 'Accounts')
                                {
                                    
                                    $q = ''
                                           . 'SELECT '
                                           . ' x_o.name AS product_name,  '
                                           . ' x_o.sales_stage AS sales_stage,  '
                                           . 'x_o.id AS opportunity_id, '
                                           . ' x_o.date_closed AS date_closed  '
                                           . 'FROM accounts AS x_a '
                                           . 'LEFT JOIN accounts_opportunities AS x_ao '
                                           . 'ON (x_ao.account_id=x_a.id AND x_ao.deleted=0) '
                                           . 'LEFT JOIN opportunities AS x_o '
                                           . 'ON (x_o.id=x_ao.opportunity_id AND x_o.deleted=0) '
                                           . 'WHERE x_a.id = \'' . $bean->parent_id . '\' '
                                           . ' AND x_o.sales_stage IN (\'Stage1\', \'Stage 2\', \'Stage 3\', \'Stage 4 \', \'Stage 5\')';
                                            $rs = $db->query($q);

                               while($row = $db->fetchByAssoc($rs)) {
                                    $opportunity =  new stdClass();
                                    $opportunity->product_name = $row['product_name'];
                                    $opportunity->date_closed = $timedate->to_display_date($row['date_closed']);
                                    $opportunity->sales_stage = $sales_stage_arr[$row['sales_stage']];
                                    $opportunity->opportunity_id = $row['opportunity_id'];
                                    $opportunities_arr[] = $opportunity;
                               }

                              }
			}


                        $teams = array();
			$team_id = "";
			$team_name = "";
			if (isPro()) {
				if (is551()) {
					require_once('modules/Teams/TeamSetManager.php');
					$teams = TeamSetManager::getTeamsFromSet($bean->team_set_id);
					$team_id = $bean->team_id;
					$team_name = $bean->team_name;
				} else {
					$team_id = $bean->team_id;
					require_once('modules/Teams/Team.php');
					$temp_team = new Team();
					$temp_team->retrieve($bean->team_id);
					$team_name = $temp_team->name;
				}
			} else {
				$team_id = "";
				$team_name = "";
			}

			if (!isset($bean->cal2_repeat_type_c)) $bean->cal2_repeat_type_c = "";
			if (!isset($bean->cal2_repeat_interval_c)) $bean->cal2_repeat_interval_c = "";
			if (!isset($bean->cal2_repeat_end_date_c)) $bean->cal2_repeat_end_date_c = "";
			if (!isset($bean->cal2_repeat_days_c)) $bean->cal2_repeat_days_c = "";
			if (!isset($bean->location)) $bean->location = "";
			if (!isset($bean->duration_hours)) $bean->duration_hours = 0;
			if (!isset($bean->duration_minutes) || $bean->duration_minutes == 0) {
				if ($bean->duration_hours == 0) {
					$bean->duration_minutes = 15;
				} else {
					$bean->duration_minutes = 0;
				}
			}
			
			$start = to_timestamp_from_uf($bean->date_start);

			$custno_c = '';
			if (strtolower($bean->parent_type) == 'accounts') {
			    $bn = new Account();
			    $bn->retrieve($bean->parent_id);
			    if(isset($bn->custno_c)) $custno_c = $bn->custno_c;
			}
			
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
				'duration_hours' => $bean->duration_hours,
				'duration_minutes' => $bean->duration_minutes,
				'reminder_time' => $bean->reminder_time,
				'status' => $bean->status,
				'location' => $bean->location,
				'time_start' => timestamp_to_user_formated2($start,$GLOBALS['timedate']->get_time_format()),
				'direction' => $bean->direction,
				'description' => $bean->description,
				'outcome_c' => $bean->outcome_c,
				'custno_c' => $custno_c,
				'cal2_category_c' => $bean->cal2_category_c,
				'cal2_options_c' => $bean->cal2_options_c,
				'cal2_whole_day_c' => $bean->cal2_whole_day_c,
				'parent_type' => $bean->parent_type,
				'parent_name' => $bean->parent_name,
				'parent_id' => $bean->parent_id,
				'team_id' => $team_id,
				'team_name' => $team_name,
				'teams' => $teams,
				'cal2_recur_id_c' => $bean->$jn,
				'cal2_repeat_type_c' => $bean->cal2_repeat_type_c,
				'cal2_repeat_interval_c' => $bean->cal2_repeat_interval_c,
				'cal2_repeat_end_date_c' => $bean->cal2_repeat_end_date_c,
				'cal2_repeat_days_c' => $bean->cal2_repeat_days_c,
                                'previous_outcome' => $previous_outcome,
                                'previous_date' => $previous_date,
                                'previous_status' => $previous_status,
                                'next_description' => $next_description,
                                'next_date' => $next_date,
                                'opportunities_arr' => $opportunities_arr,
                                'lead_account_name' => $lead_account_name,
			);

		} else {
			$json_arr = array(
				'succuss' => 'no',
			);
		}
		echo json_encode($json_arr);
	}
}
?>
