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
 *Calendar2ViewAjaxSave
 * 
 * */
 
require_once('include/MVC/View/SugarView.php');
require_once('modules/Calendar2/Calendar2.php');

class Calendar2ViewAjaxSave extends SugarView {

 	function Calendar2ViewAjaxSave(){
 		parent::SugarView();

 	}
 	
 	function process() {
		$this->display();
 	}

 	function display() {

		require_once("modules/Calls/Call.php");
		require_once("modules/Meetings/Meeting.php");

		require_once("modules/Calendar2/functions.php");

                $mk_start_save = mktime();
                //$GLOBALS['log']->test('AjaxSave start time='.$mk_start_save);

		if($_REQUEST['cur_module'] == 'Calls'){
			$bean = new Call();
                        $next_bean = new Call();
                        $table_name = $bean->table_name;
			$type = 'call';
			$jn = "cal2_call_id_c";
		}
		if($_REQUEST['cur_module'] == 'Meetings'){
			$bean = new Meeting();
                        $next_bean = new Meeting();
                        $table_name = $bean->table_name;
			$type = 'meeting';
			$jn = "cal2_meeting_id_c";
		}

		if(!empty($_REQUEST['record'])) {
			$bean->retrieve($_REQUEST['record']);
		}

//		if(!$bean->ACLAccess('Save')) {
//			$json_arr = array(
//				'succuss' => 'no',
//			);
//			echo json_encode($json_arr);
//			die;
//		}

		$bean->name = $_REQUEST['name'];
		$bean->location = (isset($_REQUEST['location']) && !empty($_REQUEST['location'])) ? $_REQUEST['location'] : "";
		$bean->date_start = $_REQUEST['date_start'];
		$bean->date_end = $_REQUEST['date_start'];
		if (!isset($_REQUEST['duration_hours'])) $_REQUEST['duration_hours'] = 0;
		$bean->duration_hours = $_REQUEST['duration_hours'];
		if (!isset($_REQUEST['duration_minutes'])) {
			if ($bean->duration_hours == 0) {
				$_REQUEST['duration_minutes'] = 15;
			} else {
				$_REQUEST['duration_minutes'] = 0;
			}
		}
		$bean->duration_minutes = $_REQUEST['duration_minutes'];
		if(isset($_REQUEST['cal2_whole_day_c']) && !empty($_REQUEST['cal2_whole_day_c'])){
			$bean->duration_hours = $_REQUEST['duration_hours_h'];
			$bean->duration_minutes = $_REQUEST['duration_minutes_h'];	
		}

		if(isset($_REQUEST['reminder_checked']) && !empty($_REQUEST['reminder_checked']))
			$bean->reminder_time = $_REQUEST['reminder_time']; 
		else
			$bean->reminder_time = -1;
		if(isset($_REQUEST['cur_module']) && $_REQUEST['cur_module'] == 'Calls')
		$bean->direction = $_REQUEST['direction'];
		$bean->status = $_REQUEST['status'];
		$bean->assigned_user_id = $_REQUEST['cal2_assigned_user_id'];
		if (isset($_REQUEST['parent_type'])) $bean->parent_type = $_REQUEST['parent_type'];
		if (isset($_REQUEST['parent_id'])) $bean->parent_id = $_REQUEST['parent_id'];
		if (isset($_REQUEST['description'])) $bean->description = $_REQUEST['description'];
		if (isset($_REQUEST['cal2_category_c'])) $bean->cal2_category_c = $_REQUEST['cal2_category_c'];
		if(!isset($_REQUEST['cal2_options_c']) || empty($_REQUEST['cal2_options_c']))
			$bean->cal2_options_c = false;
		else
			$bean->cal2_options_c = $_REQUEST['cal2_options_c'];
		if(!isset($_REQUEST['cal2_whole_day_c']) || empty($_REQUEST['cal2_whole_day_c']))
			$bean->cal2_whole_day_c = false;
		else
			$bean->cal2_whole_day_c = $_REQUEST['cal2_whole_day_c'];

		if (isPro()) {
			if (is551()) {
				require_once('modules/Teams/TeamSet.php');
				$ts = new TeamSet();
				$team_arr = array();
				for($i = 0; $i < 50; $i++)
					if(isset($_REQUEST["id_team_name_collection_".$i]) && !empty($_REQUEST["id_team_name_collection_".$i]))		
						$team_arr[] = $_REQUEST["id_team_name_collection_".$i];
				$bean->team_id = $_REQUEST["id_team_name_collection_".$_REQUEST["primary_team_name_collection"]];
				$team_set_id = $ts->addTeams($team_arr);
				$bean->team_set_id = $team_set_id;
			} else {
				$bean->team_id = $_REQUEST['cal2_team_id'];
			}
		}

		if (isset($_REQUEST['outcome_c'])) {
		    $bean->outcome_c = $_REQUEST['outcome_c']; 
		}
		
		if(((!isset($_REQUEST['cal2_recur_id_c']) || empty($_REQUEST['cal2_recur_id_c'])) && $_REQUEST['edit_all_recurrence'] == true) || (isset($_REQUEST['cal2_repeat_type_c']) && $_REQUEST['cal2_repeat_type_c'] != '')) {
			$bean->cal2_repeat_type_c = $_REQUEST['cal2_repeat_type_c'];
			$bean->cal2_repeat_interval_c = $_REQUEST['cal2_repeat_interval_c'];
			$bean->cal2_repeat_end_date_c = $_REQUEST['cal2_repeat_end_date_c'];
			$bean->cal2_repeat_days_c = $_REQUEST['cal2_repeat_days_c'];	
		}
		//vCal is updated later in invitees_filling
		$bean->update_vcal = false;
                $check_notify =(!empty($_REQUEST['send_invites']) && ($_REQUEST['send_invites'] == '1' || $_REQUEST['send_invites'] == 'on')) ? true : false;
                
                $this->invitees_filling($bean);
		
                
                $bean->save($check_notify);
                

                

		//if current_user is included in participatns, its shcedule is automatically set to 'accept'
		//$this->invitees_filling($bean);
                

		//store resource ids to update schedulerrows later
		$user_ids = array();
		foreach($bean->resources_arr as $r) $user_ids[] = $r;


                if(!empty($_REQUEST['next_description']) && $_REQUEST['next_description'] != ''){

                    global $db;
                        
                    $q_next = ''
                                           . 'SELECT '
                                           . 'id  '
                                           . 'FROM ' . $table_name . ' '
                                           . 'WHERE parent_id = \'' . $bean->parent_id . '\' '
                                           . ' AND UNIX_TIMESTAMP(date_start) > UNIX_TIMESTAMP(\''.$bean->date_start.'\')'
                                           . ' AND deleted=0 '
                                           . 'ORDER BY UNIX_TIMESTAMP(date_start) ASC LIMIT 1';
                                            $rs_next = $db->query($q_next);
                              while($row = $db->fetchByAssoc($rs_next)) {
                                  $next_bean->retrieve($row['id']);
                                  $next_bean->description = $_REQUEST['next_description'];
                                  //$GLOBALS['log']->test('AjaxSave date next description='.$next_bean->date_start);
                                  $next_bean->save();
                              }
                }

                $arr_rec = array();
		if(((!isset($_REQUEST['cal2_recur_id_c']) || empty($_REQUEST['cal2_recur_id_c'])) && $_REQUEST['edit_all_recurrence'] == true) || (isset($_REQUEST['cal2_repeat_type_c']) && $_REQUEST['cal2_repeat_type_c'] != '') ){
			$arr_rec = $this->createRecurrence($bean, $jn);
		}

                $mk_time_for_recurrence_save = mktime() - $mk_time_after_first_save;
                //$GLOBALS['log']->test('AjaxSave time for recurrence save='.$mk_time_for_recurrence_save);

		$bean->retrieve($bean->id); // do not delete this line!!! it prevents the sugar's bug with timedate!

                

		global $timedate;


		if($type == 'call') $users = $bean->get_call_users();
		if($type == 'meeting') $users = $bean->get_meeting_users();
		//store user ids to update schedulerrows on browser
		//$user_ids = array();
		foreach($users as $u) $user_ids[] = $u->id;

		$team_id = "";
		$team_name = "";
		if (isPro()) {
			$team_id = $bean->team_id;
			$team_name = $bean->team_name;
		} else {
			$team_id = "";
			$team_name = "";
		}

		//shorten time_start for dashlet
		/*if ($_REQUEST['currentmodule'] == "Home") {
			//$temp_time_start = timestamp_to_user_formated($start + $GLOBALS['timedate']->get_hour_offset() * 3600, $GLOBALS['timedate']->get_time_format(false));
			//$temp_time_start = date($GLOBALS['timedate']->get_time_format(false), $start + $GLOBALS['timedate']->get_hour_offset() * 3600);
			//$temp_time_start = date($GLOBALS['timedate']->get_time_format(false), $start + $GLOBALS['timedate']->get_hour_offset() * 3600);
			$temp_time_start = $timedate->to_display_time($bean->date_start, false);
		} else {
			//$temp_time_start = timestamp_to_user_formated($start + $GLOBALS['timedate']->get_hour_offset() * 3600, $GLOBALS['timedate']->get_time_format());
			//$temp_time_start = date($GLOBALS['timedate']->get_time_format(), $start + $GLOBALS['timedate']->get_hour_offset() * 3600);
			//$temp_time_start = date($GLOBALS['timedate']->get_time_format(), $start + $GLOBALS['timedate']->get_hour_offset() * 3600);
			$temp_time_start = $timedate->to_display_time($bean->date_start);
		}*/
		
		$start = to_timestamp_from_uf($bean->date_start);

		$loc = (!is_null($bean->location)) ? $bean->location : "";
		$repeat_type = (isset($_REQUEST['cal2_repeat_type_c'])) ? $_REQUEST['cal2_repeat_type_c'] : "";
		$repeat_interval = (isset($_REQUEST['cal2_repeat_interval_c'])) ? $_REQUEST['cal2_repeat_interval_c'] : "";
		$repeat_end_date = (isset($_REQUEST['cal2_repeat_end_date_c'])) ? $_REQUEST['cal2_repeat_end_date_c'] : "";
		$repeat_days = (isset($_REQUEST['cal2_repeat_days_c'])) ? $_REQUEST['cal2_repeat_days_c'] : "";
		$recur_id = (isset($_REQUEST['cal2_recur_id_c'])) ? $_REQUEST['cal2_recur_id_c'] : "";

		$custno_c = '';
		$customer_name = '';
		$lead_name = '';
                $lead_account_name = '';

		$parent_type = strtolower($bean->parent_type);

        if ($parent_type == 'accounts') {
            global $moduleList,$beanList,$beanFiles;
            $class_name = $beanList['Accounts'];
            $class_file_path = $beanFiles[$class_name];
            require_once $class_file_path;
            $o = new $class_name();
            $o->retrieve($bean->parent_id);

            if (isset($o->custno_c)) $custno_c = (string) $o->custno_c;
            if (isset($o->name)) $customer_name = (string) $o->name;
        }

        if ($parent_type == 'leads') {
            global $moduleList,$beanList,$beanFiles;
            $class_name = $beanList['Leads'];
            $class_file_path = $beanFiles[$class_name];
            require_once $class_file_path;
            $o = new $class_name();
            $o->retrieve($bean->parent_id);

            $lead_name = (string) $o->name;
            $lead_account_name = (string) $o->account_name;
        }

        require_once 'modules/Calendar2/class.Calendar2_WidgetTitle.php';
        $widget_title =  Calendar2_WidgetTitle::create(
            $bean->parent_type,
            $bean->parent_id,
            $customer_name,
            $lead_name,
            $bean->name,
            $bean->status,
            $lead_account_name,
            $custno_c

        );

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
			'cal2_recur_id_c' => $recur_id,
			'cal2_repeat_type_c' => $repeat_type,
			'cal2_repeat_interval_c' => $repeat_interval,
			'cal2_repeat_end_date_c' => $repeat_end_date,
			'cal2_repeat_days_c' => $repeat_days,
			'cal2_category_c' => $bean->cal2_category_c,
			'arr_rec' => $arr_rec,
			'detailview' => 1,
		    'outcome_c' => $bean->outcome_c,
		    'parent_type' => $bean->parent_type,
		    'parent_id' => $bean->parent_id,
		    'custno_c' => $custno_c,
		    'customer_name' => $customer_name,
            'widget_title' => $widget_title
		);

		if($bean->object_name == 'Call') {// return direction if has
			$json_arr['direction'] = $bean->direction;		
		}

		echo json_encode($json_arr);
	}	

	function invitees_filling(&$bean){

		global $current_user;

		$type = "";
		if($bean->object_name == 'Call'){
			$type = 'call';
		} elseif($bean->object_name == 'Meeting'){
			$type = 'meeting';
		}

		if(!empty($_POST['user_invitees']))
			$userInvitees = explode(',', trim($_POST['user_invitees'], ','));
		else 
			$userInvitees = array();

		
                $deleteUsers = array();
		$bean->load_relationship('users');
                

		//Finding removed users and storing existing accept status
	    //we call get_meeting_users instead of link->get to get accept status
		$userlist = array();
		if ($type == "meeting") {
			$userlist = $bean->get_meeting_users();
		} elseif ($type == "call") {
			$userlist = $bean->get_call_users();
		}
		for ($i=0; $i<count($userlist); $i++) {
			if (!in_array(($userlist[$i]->id), $userInvitees)) {
				$deleteUsers[$userlist[$i]->id] = $userlist[$i]->id;
				//$GLOBALS['log']->debug("Going to delete users deleteUsers=".$userlist[$i]->id);
				$bean->users->delete($bean->id, $userlist[$i]->id);
				//$GLOBALS['log']->debug("Going to call vCal:cache on deletion user_id=".$userlist[$i]->id);
				vCal2::cache_sugar_vcal($userlist[$i]);
			} else {
				$acceptStatusUsers[$userlist[$i]->id] = $userlist[$i]->accept_status;
				//$GLOBALS['log']->debug("Going to delete users acceptStatus=".$acceptStatusUsers[$userlist[$i]->id]);
			}
		}

                
		//Finding removed resources	
		if(!empty($_POST['resources_assigned'])) 
			$resourcesAssigned = explode(',', trim($_POST['resources_assigned'], ','));
		else 
			$resourcesAssigned = array();

		$deleteResources = array();
		$bean->load_relationship('resources');
		$reslist = array();
		$reslist = $bean->resources->get(false);
		for ($i=0; $i<count($reslist); $i++) {
			if (!in_array(($reslist[$i]), $resourcesAssigned))
				$deleteResources[$reslist[$i]] = $reslist[$i];
		}

		if(count($deleteResources) > 0){
				$GLOBALS['log']->debug("Going to call count=".count($deleteResources));
			foreach($deleteResources as $r) {
				$GLOBALS['log']->debug("Going to call vCals r=".$r);
				$bean->resources->delete($bean->id, $r);
				require_once('modules/Resources/Resource.php');
				$res = new Resource();
				$res->retrieve($r);
				$GLOBALS['log']->debug("Going to call vCal:cache on deletion resource_id=".$r);
				vCal2::cache_sugar_vcal($res);
			}
		}

		//Now relations to users and resources are being rebuilt, then vCals are updated.
                //$GLOBALS['log']->test('AjaxSave we are here 371');
		
		$bean->users_arr = $userInvitees;
		$bean->resources_arr = $resourcesAssigned;
                //$GLOBALS['log']->test('AjaxSave we are here 375    '.$userInvitees[0]);
                //$GLOBALS['log']->test('AjaxSave we are here 376    '.$userInvitees[1]);

		$existing_users = array();
		if(!empty($_POST['existing_invitees'])) 
			$existing_users =  explode(",", trim($_POST['existing_invitees'], ','));	

		foreach($bean->users_arr as $user_id){
			if(empty($user_id))	continue;
			if(!isset($existing_users[$user_id]) && !isset($deleteUsers[$user_id])) {
				if(!isset($acceptStatusUsers[$user_id]) && $user_id != $current_user->id) {
					require_once('modules/Users/User.php');
					$temp_user = new User();
					$temp_user->retrieve($user_id);
					if($temp_user->getPreference('auto_accept','global',$temp_user) == 'true') {
						$bean->users->add($user_id, array('accept_status'=>'accept'));
					} else {
						$bean->users->add($user_id);
					}
				} else {
					if ($user_id == $current_user->id) {
						$bean->users->add($user_id, array('accept_status'=>'accept'));
					} else {
						$bean->users->add($user_id, array('accept_status'=>$acceptStatusUsers[$user_id]));
					}
				}
			}
			
                        
                        //Updates each user's vCal
			require_once('modules/Users/User.php');
			$GLOBALS['log']->debug("Going to call vCal:cache on deletion user_id=".$user_id);
			$usr = new User();
                       	$usr->retrieve($user_id);
                        //$GLOBALS['log']->test('AjaxSave we are here 410');
			vCal2::cache_sugar_vcal($usr);
                        //$GLOBALS['log']->test('AjaxSave we are here 412');
		}
		
		//Rebuilding relations to resources, and vCals are updated.
		$existing_resources =  array();
		if(!empty($_POST['existing_resources_assigned'])) 
			$existing_resources =  explode(",", trim($_POST['existing_resources_assigned'], ','));

			foreach($bean->resources_arr as $resource_id){
			if(empty($resource_id) || isset($exiting_resources[$resource_id]) || isset($deleteResources[$resource_id])) 
				continue;
			
			$bean->resources->add($resource_id, array('accept_status'=>'accept'));

			require_once('modules/Resources/Resource.php');
			$res = new Resource();
			$res->retrieve($resource_id);
			$GLOBALS['log']->debug("Going to call vCal:cache resource_id=".$resource_id);
			vCal2::cache_sugar_vcal($res);
		}
	}
	
	function createRecurrence(&$bean, $jn) {

		require_once("modules/Calendar2/functions.php");

		$ret_arr = array();
		$repeat_days = "";
		$rtype = "";
		
		
		if (isset($_REQUEST['cal2_repeat_days_c']) && !empty($_REQUEST['cal2_repeat_days_c'])) $repeat_days = $_REQUEST['cal2_repeat_days_c'];
		
		if (isset($_REQUEST['cal2_repeat_type_c']) && !empty($_REQUEST['cal2_repeat_type_c'])) $rtype = $_REQUEST['cal2_repeat_type_c'];
		
		if (isset($_REQUEST['cal2_repeat_interval_c']) && !empty($_REQUEST['cal2_repeat_interval_c'])) {
			$interval = $_REQUEST['cal2_repeat_interval_c'];
		} else {
			$interval = 0;
		}

		$timezone = $GLOBALS['timedate']->getUserTimeZone();
		global $timedate;


		$start_unix = to_timestamp_from_uf($_REQUEST['date_start']);

		$end_unix = to_timestamp_from_uf($_REQUEST['cal2_repeat_end_date_c']);
		$end_unix = $end_unix + 60*60*24 - 1;



		$start_day = date("w",$start_unix - date('Z',$start_unix)) + 1;
		$start_dayM = date("j",$start_unix - date('Z',$start_unix));
		$GLOBALS['log']->debug('view.ajaxsave.php start_day='.$start_day);
		$GLOBALS['log']->debug('view.ajaxsave.php start_dayM='.$start_dayM);
		$GLOBALS['log']->debug('view.ajaxsave.php interval='.$interval);
		$GLOBALS['log']->debug('view.ajaxsave.php rtype='.$rtype);
		$GLOBALS['log']->debug('view.ajaxsave.php repeat_days='.$repeat_days);

		$qu = " 
			UPDATE	".$bean->table_name." t
			SET t.deleted = 1 	 
			WHERE t.".$jn." = '".addslashes($_REQUEST['record'])."'
		";	

		$bean->db->query($qu);
		$ft = true;

		if(!empty($rtype) && (!isset($_REQUEST['cal2_repeat_end_date_c']) || !empty($_REQUEST['cal2_repeat_end_date_c'])) ){
			
			if(empty($interval) || $interval == 0)
				$interval = 1;

			$cur_date = $start_unix;
			$GLOBALS['log']->debug('view.ajaxsave.php cur_date='.$cur_date);
			$GLOBALS['log']->debug('view.ajaxsave.php cur_date formatted='.date("F j, Y, g:i a", $cur_date));
			
			$i = 0;
			if($rtype == 'Weekly' || $rtype == 'Monthly (day)')
				$i--;

			while($cur_date <= $end_unix){

				$i++;
				
				if($rtype == 'Daily')
					$step = 60*60*24;
				if($rtype == 'Monthly (date)'){
					$step = 60*60*24 * date('t',$cur_date - date('Z',$cur_date));
					$this_month = date('n',$cur_date - date('Z',$cur_date));
					$next_month = date('n',$cur_date + $step - date('Z',$cur_date));	
					if($next_month - $this_month == 2 || $next_month - $this_month == -10){	
						$day_number = intval(date('d',$cur_date + $step - date('Z',$cur_date))) + 1;							
						$step += 60*60*24 * date('t',$cur_date + $step - $day_number*24*3600 - date('Z',$cur_date));
						$i++;
					}
				}
				if($rtype == 'Yearly'){
					$step = 60*60*24 * 365;
					if( date('d',$cur_date + $step - date('Z',$cur_date)) !=  date('d',$cur_date - date('Z',$cur_date)) )
						$step += 60*60*24;
				}
				
				if($rtype == 'Weekly'){
					$step = 60*60*24*7;
					//sunday of the week
					$week_start_day = $start_unix - ($start_day -1)*60*60*24 + $step * $i;
					$GLOBALS['log']->debug('view.ajaxsave.php weekly: week_start_day='.date("F j, Y, g:i a", $week_start_day));

					//if($i % $interval == 0)
					if($i > 0 && $i % $interval == 0)
						//for($d = $start_day; $d < 7; $d++)
						for($d = 1; $d < 8; $d++)
							//if(strpos($repeat_days,(string)($d + 1)) !== false){
							if(strpos($repeat_days,(string)($d)) !== false){
								//if($cur_date + $d*60*60*24 > $end_unix)
								if($week_start_day + ($d-1)*60*60*24 > $end_unix)
									break;
								$GLOBALS['log']->debug('view.ajaxsave.php for createClone week_start_date='.date("F j, Y, g:i a", $week_start_day));
								//$ret_arr[] = $this->create_clone($bean,$cur_date + ($d)*60*60*24,$jn);
								$ret_arr[] = $this->create_clone($bean, $week_start_day + ($d - 1)*60*60*24, $jn);
							}
					//$start_day = 0;
				}

				if($rtype == 'Monthly (day)'){
					$step = 60*60*24 * date('t',$cur_date - date('Z',$cur_date));

					if($i % $interval == 0 && $start_dayM <= $start_day)
						for($d = $start_day; $d < 7; $d++){
							$dd = date('w',$cur_date + $d*60*60*24 - date('Z',$cur_date));
							if(strpos($repeat_days,(string)($dd + 1)) !== false){
								//$ST_curr = getDST($cur_date + $d*60*60*24);		
								//$cur_date += ($ST_prev - $ST_curr)*3600;
								//$ST_prev = getDST($cur_date + $d*60*60*24);
								if($cur_date + $d*60*60*24 > $end_unix)
									break;
								$ret_arr[] = $this->create_clone($bean,$cur_date + $d*60*60*24,$jn);
							}
						}
					$start_day = 0;
					$start_dayM = 0;
				}

				$cur_date += $step;

				//$ST_curr = getDST($cur_date);		
				//$cur_date += ($ST_prev - $ST_curr)*3600;
				//$ST_prev = getDST($cur_date);
				
				if($i % $interval != 0)
					continue;
				
				if($cur_date > $end_unix)
					break;
					
				if($rtype == 'Weekly' || $rtype == 'Monthly (day)')
					continue;		

				$ret_arr[] = $this->create_clone($bean,$cur_date,$jn);

			}
		}
		
	 return $ret_arr;
	
	}
	
	function create_clone($bean,$cur_date,$jn){

		$GLOBALS['log']->debug('view.ajaxsave.php create_clone: cur_date formatted='.date("F j, Y, g:i a", $cur_date));

		$obj = $this->clone_rec($bean);	
		$obj->date_start = timestamp_to_user_formated2($cur_date);
		$obj->date_end = timestamp_to_user_formated2($cur_date);	
		$GLOBALS['log']->debug('view.ajaxsave.php create_clone: date_start='.$obj->date_start);
		$obj->$jn = $bean->id;
                $check_notify =(!empty($_REQUEST['send_invites']) && $_REQUEST['send_invites'] == 'on') ? true : false;
                //$this->invitees_filling($obj);
		$obj->save($check_notify);
		$obj_id = $obj->id;
		
		$obj->retrieve($obj_id);
				
		//$this->invitees_filling($obj);
		unset($obj);
		$date_unix = $cur_date;
		return 	array(
				'record' => $obj_id,
				'start' => $date_unix,
			);
	}

	function clone_rec($bean) {
		$obj = new $bean->object_name();
		$obj->name = $bean->name;
		$obj->duration_hours = $bean->duration_hours;
		$obj->duration_minutes = $bean->duration_minutes;
		$obj->reminder_time = $bean->reminder_time;
		if($obj->object_name == 'Call')
			$obj->direction = $bean->direction; 
		$obj->status = $bean->status;
		$obj->location = $bean->location;
		$obj->assigned_user_id = $bean->assigned_user_id;
		$obj->parent_type = $bean->parent_type;
		$obj->parent_id = $bean->parent_id;
		$obj->description = $bean->description;
		$obj->cal2_category_c = $bean->cal2_category_c;
		$obj->cal2_options_c = $bean->cal2_options_c;
		$obj->cal2_whole_day_c = $bean->cal2_whole_day_c;

                $obj->users_arr = $bean->users_arr;
		$obj->resources_arr = $bean->resources_arr;
		
		if (isPro()) {
			$obj->team_id = $bean->team_id;
			if (is551()) {
				$obj->team_set_id = $bean->team_set_id;
			}
		}
		
		return $obj;
	}
	
}

