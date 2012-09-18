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

class Calendar2ViewAjaxSplit extends SugarView {

 	private  $outcomes_arr = array();
        private  $statuses_arr = array();
        private  $descriptions_arr = array();
        private $parent_types_arr = array();
        private $parent_ids_arr = array();


        function Calendar2ViewAjaxSplit(){
 		parent::SugarView();

 	}

 	function process() {
		$this->display();
 	}

 	function display() {

		require_once("modules/Calls/Call.php");
		require_once("modules/Meetings/Meeting.php");

		require_once("modules/Calendar2/functions.php");

                $mk_start_split_save = mktime();
                //$GLOBALS['log']->test('AjaxSplit start time for split save='.$mk_start_split_save);

		if($_REQUEST['cur_module'] == 'Calls'){
			$bean = new Call();
                        $clicked_bean = new Call();
                        $old_bean = new Call();
			$type = 'call';
			$jn = "cal2_call_id_c";
		}
		if($_REQUEST['cur_module'] == 'Meetings'){
			$bean = new Meeting();
                        $clicked_bean = new Meeting();
                        $old_bean = new Meeting();
			$type = 'meeting';
			$jn = "cal2_meeting_id_c";
		}

                if(!empty($_REQUEST['clicked_record'])) {
			$clicked_bean->retrieve($_REQUEST['clicked_record']);
		}

                if(!empty($_REQUEST['record'])) {
			$old_bean->retrieve($_REQUEST['record']);
		}

                



                //Preparing params of split setting
                $split_cal2_assigned_user_id = $old_bean->assigned_user_id;     // $_REQUEST['cal2_assigned_user_id']
                $split_cal2_category_c = $old_bean->cal2_category_c;            //$_REQUEST['cal2_category_c']
                $split_cal2_recur_id_c = $old_bean->$jn;                        //$_REQUEST['cal2_recur_id_c']
                $split_cal2_repeat_days_c = $old_bean->cal2_repeat_days_c;      //$_REQUEST['cal2_repeat_days_c']
                $split_cal2_repeat_end_date_c = date("m/d/Y",to_timestamp_from_uf($clicked_bean->date_start) - 60*60*24);
                $split_date = date("m/d/Y",to_timestamp_from_uf($clicked_bean->date_start));
                $split_cal2_repeat_interval_c = $old_bean->cal2_repeat_interval_c; //$_REQUEST['cal2_repeat_interval_c']
                $split_cal2_repeat_type_c = $old_bean->cal2_repeat_type_c;      //$_REQUEST['cal2_repeat_type_c'];
                //$_REQUEST['cur_module']
                $split_date_start = $old_bean->date_start;                      //$_REQUEST['date_start']
                $split_description = $old_bean->description;                    //$_REQUEST['description']
                $split_direction = $old_bean->direction;                        //$_REQUEST['direction']
                $split_duration_hours = $old_bean->duration_hours;              //$_REQUEST['duration_hours']
                $split_duration_minutes = $old_bean->duration_minutes;          //$_REQUEST['duration_minutes']
                $split_cal2_whole_day_c = $old_bean->cal2_whole_day_c;
                
                $split_name = $old_bean->name;                                  //$_REQUEST['name']
                $split_outcome_c  = $old_bean->outcome_c;                       //$_REQUEST['outcome_c']
                $split_parent_id = $old_bean->parent_id;                        //$_REQUEST['parent_id']
                //$_REQUEST['parent_name']
                //$_REQUEST['parent_name_custno_c']
                $split_parent_type = $old_bean->parent_type;                    //$_REQUEST['parent_type']
                $split_reminder_time = $old_bean->reminder_time;                //$_REQUEST['reminder_time']
                //$_REQUEST['resources_assigned']
                //$_REQUEST['return_module']
                $split_status = $old_bean->status;                              //$_REQUEST['status']
                


//		if(!$bean->ACLAccess('Save')) {
//			$json_arr = array(
//				'succuss' => 'no',
//			);
//			echo json_encode($json_arr);
//			die;
//		}

		$bean->name = $split_name;                                      //$_REQUEST['name'];
                $bean->location = $old_bean->location;
		$bean->date_start = $split_date_start;                          //$_REQUEST['date_start'];
		$bean->date_end = $split_date_start;                            //$_REQUEST['date_start'];
		$bean->duration_hours = $old_bean->duration_hours;              //$_REQUEST['duration_hours'];
		$bean->duration_minutes = $old_bean->duration_minutes;          //$_REQUEST['duration_minutes'];
                $bean->cal2_whole_day_c = $old_bean->cal2_whole_day_c;
                $bean->reminder_time = $old_bean->reminder_time;
		if(isset($_REQUEST['cur_module']) && $_REQUEST['cur_module'] == 'Calls')
		$bean->direction = $old_bean->direction;                        //$_REQUEST['direction'];
		$bean->status = $old_bean->status;                              //$_REQUEST['status'];
		$bean->assigned_user_id = $old_bean->assigned_user_id;          //$_REQUEST['cal2_assigned_user_id'];
                $bean->parent_type = $old_bean->parent_type;
                $bean->parent_id = $old_bean->parent_id;
                $bean->description = $old_bean->description;
                $bean->cal2_category_c = $old_bean->cal2_category_c;
                $bean->cal2_whole_day_c = $old_bean->cal2_whole_day_c;
		if (isPro()) {
                    $bean->team_set_id = $old_bean->team_set_id;
                    $bean->team_id = $old_bean->team_id;
		}


                $bean->outcome_c = $old_bean->outcome_c;
                $bean->cal2_repeat_type_c = $old_bean->cal2_repeat_type_c;
        	$bean->cal2_repeat_interval_c = $old_bean->cal2_repeat_interval_c;
		$bean->cal2_repeat_end_date_c = $split_cal2_repeat_end_date_c;
		$bean->cal2_repeat_days_c = $old_bean->cal2_repeat_days_c;

		//vCal is updated later in invitees_filling
		$bean->update_vcal = false;
		$bean->save();

                $mk_time_for_first_split_save = mktime() - $mk_start_split_save;
                $mk_time_after_first_split_save = mktime();
                //$GLOBALS['log']->test('AjaxSplit for first split save='.$mk_time_for_first_split_save);

		//if current_user is included in participatns, its shcedule is automatically set to 'accept'
                $bean->users_arr = explode(',', trim($old_bean->users_arr, ','));
		$bean->resources_arr = explode(',', trim($old_bean->resources_arr, ','));
                $userInvitees = explode(',', trim($old_bean->users_arr, ','));
                $resourcesAssigned = explode(',', trim($old_bean->resources_arr, ','));

                global $timedate;
                if (!$timedate) {
                    $timedate = new TimeDate();
                }

                 //outcomes saving
                $outcomes_qu = ''
                    . 'SELECT '
                    . 'outcome_c, status, description, parent_type, parent_id, date_start '
                    . 'FROM '.$old_bean->table_name.' '
                    . 'WHERE '.$jn.' = \''.addslashes($_REQUEST['record']).'\' ';
                 $rs = $old_bean->db->query($outcomes_qu);
                while($row = $old_bean->db->fetchByAssoc($rs)) {
                    $this->outcomes_arr[$timedate->to_display_date_time($row['date_start'])] =$row['outcome_c'];
                    $this->statuses_arr[$timedate->to_display_date_time($row['date_start'])] =$row['status'];
                    $this->descriptions_arr[$timedate->to_display_date_time($row['date_start'])] =$row['description'];
                    $this->parent_types_arr[$timedate->to_display_date_time($row['date_start'])] =$row['parent_type'];
                    $this->parent_ids_arr[$timedate->to_display_date_time($row['date_start'])] =$row['parent_id'];

                    //$GLOBALS['log']->test('AjaxSplit  saving date_start='.$timedate->to_display_date_time($row['date_start']));
                    //$GLOBALS['log']->test('AjaxSplit saving outcomes='.$this->outcomes_arr[$timedate->to_display_date_time($row['date_start'])]);
                   }
                //store resource ids to update schedulerrows later
		$user_ids = array();
		foreach($bean->resources_arr as $r) $user_ids[] = $r;

		$arr_rec = array();
                $arr_rec = $this->createRecurrence($bean, $jn, $split_cal2_repeat_days_c, $split_cal2_repeat_type_c, $split_cal2_repeat_interval_c, $split_date_start, $split_cal2_repeat_end_date_c);

		$mk_time_for_recurrence_split_save = mktime() - $mk_time_after_first_split_save;
                $mk_time_after_recurrence_split_save = mktime();

                //$GLOBALS['log']->test('AjaxSplit time for recurrence split save='.$mk_time_for_recurrence_split_save);
                $bean->retrieve($bean->id); // do not delete this line!!! it prevents the sugar's bug with timedate!


		global $timedate;


		if($type == 'call') $users = $old_bean->get_call_users();
		if($type == 'meeting') $users = $old_bean->get_meeting_users();
		//store user ids to update schedulerrows on browser

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


		$start = to_timestamp_from_uf($bean->date_start);

		$loc = (!is_null($bean->location)) ? $bean->location : "";

                $repeat_type = $split_cal2_repeat_type_c;
                $repeat_interval = $split_cal2_repeat_interval_c;
                $repeat_end_date = $split_cal2_repeat_end_date_c;
                $repeat_days  = $split_cal2_repeat_days_c;
                //$recur_id ='';


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

            $custno_c = (string) $o->custno_c;
            $customer_name = (string) $o->name;
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

        $mk_time_before_remove_old = mktime() - $mk_time_after_recurrence_split_save;
        $mk_time_before_remove_old_1 = mktime();
        //$GLOBALS['log']->test('AjaxSplit time before removing='.$mk_time_before_remove_old);

       

        /////removing old_bean
        $qu = "
			UPDATE	".$old_bean->table_name." t
			SET t.deleted = 1
			WHERE t.".$jn." = '".addslashes($_REQUEST['record'])."' OR t.id='".addslashes($_REQUEST['record'])."'
		";

		$old_bean->db->query($qu);

        $mk_time_for_remove_old = mktime() - $mk_time_before_remove_old_1;
        $mk_time_after_remove_old = mktime();
        //$GLOBALS['log']->test('AjaxSplit time for removing old='.$mk_time_for_remove_old);

       global $current_user;

        foreach($userInvitees as $user_id) {
                //Updates each user's vCal
                if($user_id == $current_user->id) continue;
                require_once('modules/Users/User.php');
                $temp_usr = new User();
                $temp_usr->retrieve($user_id);
                vCal::cache_sugar_vcal($temp_usr);
        }
        require_once('modules/Resources/Resource.php');
        foreach($resourcesAssigned as $res_id) {
                $temp_res = new Resource();
                $temp_res->retrieve($res_id);
                vCal2::cache_sugar_vcal($res);
        }

        $mk_time_before_sending = mktime() - $mk_time_after_remove_old;
        //$GLOBALS['log']->test('AjaxSplit time before esnding='.$mk_time_before_sending);

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
			'arr_rec' => $arr_rec,
			'detailview' => 1,
		    'outcome_c' => $bean->outcome_c,
		    'parent_type' => $bean->parent_type,
		    'parent_id' => $bean->parent_id,
		    'custno_c' => $custno_c,
		    'customer_name' => $customer_name,
            'widget_title' => $widget_title,
                    'split_date' => $split_date
		);

		echo json_encode($json_arr);
	}


	function createRecurrence(&$bean, $jn, $repeat_days, $rtype, $interval , $daystart, $dayend ) {

		require_once("modules/Calendar2/functions.php");

		$ret_arr = array();
		

		$timezone = $GLOBALS['timedate']->getUserTimeZone();
		global $timedate;


		$start_unix = to_timestamp_from_uf($daystart);

		$end_unix = to_timestamp_from_uf($dayend);
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

		if(!empty($rtype) && (!isset($dayend) || !empty($dayend)) ){

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
								$ret_arr[] = $this->create_clone($bean, $week_start_day + ($d - 1)*60*60*24, $jn,$old_bean);
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
								$ret_arr[] = $this->create_clone($bean,$cur_date + $d*60*60*24,$jn,$old_bean);
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

				$ret_arr[] = $this->create_clone($bean,$cur_date,$jn,$old_bean);

			}
		}

	 return $ret_arr;

	}

	function create_clone(&$bean,$cur_date,$jn,&$old_bean){

		$GLOBALS['log']->debug('view.ajaxsave.php create_clone: cur_date formatted='.date("F j, Y, g:i a", $cur_date));

		$obj = $this->clone_rec($bean);
		$obj->date_start = timestamp_to_user_formated2($cur_date);
		$obj->date_end = timestamp_to_user_formated2($cur_date);
                $obj->outcome_c = $this->outcomes_arr[$obj->date_start];
                $obj->status = $this->statuses_arr[$obj->date_start];
                $obj->description = $this->descriptions_arr[$obj->date_start];
                $obj->parent_type = $this->parent_types_arr[$obj->date_start];
                $obj->parent_id = $this->parent_ids_arr[$obj->date_start];
                
		$GLOBALS['log']->debug('view.ajaxsave.php create_clone: date_start='.$obj->date_start);
		$obj->$jn = $bean->id;
		$obj->save();
		$obj_id = $obj->id;
                $obj_outcome = $obj->outcome_c;
                $obj_status = $obj->status;
                $obj_desc = $obj->description;



		$obj->retrieve($obj_id);

                $obj_customer_name = '';
		$obj_lead_name = '';
                $obj_custno_c = '';
                $obj_lead_account_name = '';

         if (strtolower($obj->parent_type) == 'accounts') {
            global $moduleList,$beanList,$beanFiles;
            $class_name = $beanList['Accounts'];
            $class_file_path = $beanFiles[$class_name];
            require_once $class_file_path;
            $o = new $class_name();
            $o->retrieve($obj->parent_id);

            $obj_custno_c = (string) $o->custno_c;
            $obj_customer_name = (string) $o->name;
        }

        if (strtolower($obj->parent_type) == 'leads') {
            global $moduleList,$beanList,$beanFiles;
            $class_name = $beanList['Leads'];
            $class_file_path = $beanFiles[$class_name];
            require_once $class_file_path;
            $o = new $class_name();
            $o->retrieve($obj->parent_id);

            $obj_lead_name = (string) $o->name;
            $obj_lead_account_name = (string) $o->account_name;
        }

//        $GLOBALS['log']->test('AjaxSplit obj_customer_name='.$obj_customer_name);
//        $GLOBALS['log']->test('AjaxSplit obj_lead_name='.$obj_lead_name);
//        $GLOBALS['log']->test('AjaxSplit parent type='.$obj->parent_type);
//        $GLOBALS['log']->test('AjaxSplit parent id='.$obj->parent_id);
//        $GLOBALS['log']->test('AjaxSplit name='.$obj->name);


require_once 'modules/Calendar2/class.Calendar2_WidgetTitle.php';
                $obj_widget_title =  Calendar2_WidgetTitle::create(
                                $obj->parent_type,
                                $obj->parent_id,
                                $obj_customer_name,
                                $obj_lead_name,
                                $obj->name,
                                $obj->status,
                                $obj_lead_account_name,
                                $obj_custno_c
                );



		$date_unix = $cur_date;
		return 	array(
				'record' => $obj_id,
				'start' => $date_unix,
                                'status' => $obj_status,
                                'desc' => $obj_desc,
                                'widget_title' => $obj_widget_title,
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
                $obj->users_arr = explode(',', trim($bean->users_arr, ','));
		$obj->resources_arr = explode(',', trim($bean->resources_arr, ','));

		if (isPro()) {
			$obj->team_id = $bean->team_id;
			if (is551()) {
				$obj->team_set_id = $bean->team_set_id;
			}
		}

		return $obj;
	}


}

