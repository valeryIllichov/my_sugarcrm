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
/*********************************************************************************
* Portions created by SugarCRM are Copyright (C) SugarCRM, Inc. All Rights Reserved.
********************************************************************************/
require_once('modules/Calendar2/DateTimeUtil.php');
require_once('modules/Calendar2/functions.php');
require_once('modules/vCals/vCal.php');
require_once('modules/Calendar/Calendar.php');
require_once('modules/Meetings/Meeting.php');
require_once('modules/Calls/Call.php');
require_once('modules/Tasks/Task.php');
require_once('modules/Resources/Resource.php');
require_once('include/utils/activity_utils.php');

class Calendar2 extends SugarBean
{
	var $view = 'day';
	var $first_day_of_a_week;
	var $date_time;
	var $slices_arr = array();
        // for monthly calendar view, if you want to see all the
        // days in the grid, otherwise you only see that months
	var $show_only_current_slice = false;
	var $show_activities = true;
	var $show_tasks = true;
	var $activity_focus;
        var $show_week_on_month_view = true;
	var $use_24 = 1;
	var $toggle_appt = true;
	var $slice_hash = array();
	var $shared_users_arr = array();
	var $disable_vardef = true;
	
	var $acts_arr2 = array(); // letrium yura

	function Calendar2($view='day', $time_arr=array())
	{
		global $current_user;
		global $sugar_config;
		
		if ( $current_user->getPreference('time'))
		{
			$time = $current_user->getPreference('time');
		}
		else
		{
			$time = $sugar_config['default_time_format'];
		}

		if ( $current_user->getPreference('week_start_day', 'global', $current_user))
		{
			$first_day_of_a_week = $current_user->getPreference('week_start_day', 'global', $current_user);
		}
		else
		{
			$first_day_of_a_week = 'Sunday';
		}

		if( substr_count($time, 'h') > 0)
		{
			$this->use_24 = 0;
		}

		$this->view = $view;

		if ( isset($time_arr['activity_focus']))
		{
			$this->activity_focus =  new Calendar2Activity($time_arr['activity_focus']);
			$this->date_time =  $this->activity_focus->start_time;
		}
		else
		{
			$this->date_time = new DateTimeUtil2($time_arr,true);
		}

		if (!( $view == 'day' || $view == 'month' || $view == 'year' || $view == 'week' || $view == 'shared') )
		{
			sugar_die ("view needs to be one of: day, week, month, shared, or year");
		}

		if ( empty($this->date_time->year))
		{
			sugar_die ("all views: year was not set");
		}
		else if ( $this->view == 'month' &&  empty($this->date_time->month))
		{
			sugar_die ("month view: month was not set");
		}
		else if ( $this->view == 'week' && empty($this->date_time->week))
		{
			sugar_die ("week view: week was not set");
		}
		else if ( $this->view == 'shared' && empty($this->date_time->week))
		{
			sugar_die ("shared view: shared was not set");
		}
		else if ( $this->view == 'day' &&  empty($this->date_time->day) && empty($this->date_time->month))
		{
			sugar_die ("day view: day and month was not set");
		}
		
		// monday start fix
		global $first_day_of_a_week;
		//echo $this->day_of_week;
		if(($view == 'week' || $view == 'shared') && $first_day_of_a_week == "Monday" && $this->date_time->day_of_week == 0){
			$this->date_time = $this->date_time->get_yesterday();
			header("Location: index.php?action=index&module=Calendar2&view=".$this->view."&".$this->date_time->get_date_str());
		}
		// end monday start fix

		$this->create_slices();

	}
	function add_shared_users(&$shared_users_arr)
	{
		$this->shared_users_arr = $shared_users_arr;
	}

	function get_view_name($view)
	{
		if ($view == 'month')
		{
			return "MONTH";
		}
		else if ($view == 'week')
		{
			return "WEEK";
		}
		else if ($view == 'day')
		{
			return "DAY";
		}
		else if ($view == 'year')
		{
			return "YEAR";
		}
		else if ($view == 'shared')
		{
			return "SHARED";
		}
		else
		{
			sugar_die ("get_view_name: view ".$this->view." not supported");
		}
	}

    function isDayView() {
        return $this->view == 'day';
    }

	function get_slices_arr()
	{
		return $this->slices_arr;
	}


	function create_slices()
	{

		global $current_user;


		if ( $this->view == 'month')
		{
			$days_in_month = $this->date_time->days_in_month;

			
			$first_day_of_month = $this->date_time->get_day_by_index_this_month(0);
			$num_of_prev_days = $first_day_of_month->day_of_week;
			// do 42 slices (6x7 grid)
			
			global $first_day_of_a_week;
			$ifrom = 0;
			$ito = 42;
			if($first_day_of_a_week == "Monday"){
				if ($num_of_prev_days == 0) 
					$num_of_prev_days = 7; 
				$ifrom++; $ito++;	
			}


			for($i=$ito;$i < $ito;$i++)
			{
				$slice = new Slice2('day',$this->date_time->get_day_by_index_this_month($i-$num_of_prev_days));
				$this->slice_hash[$slice->start_time->get_mysql_date()] = $slice;
				array_push($this->slices_arr,  $slice->start_time->get_mysql_date());
			}

		}
		else if ( $this->view == 'week' || $this->view == 'shared')
		{
			$days_in_week = 7;
			
			global $first_day_of_a_week;
			$ioffset = 0;
			if($first_day_of_a_week == "Monday")
				$ioffset = 1 ;
			

			for($i=0;$i<$days_in_week;$i++)
			{
				$slice = new Slice2('day',$this->date_time->get_day_by_index_this_week($i + $ioffset));
				$this->slice_hash[$slice->start_time->get_mysql_date()] = $slice;
				array_push($this->slices_arr,  $slice->start_time->get_mysql_date());
			}
		}
		else if ( $this->view == 'day')
		{
			$hours_in_day = 24;

			for($i=0;$i<$hours_in_day;$i++)
			{
				$slice = new Slice2('hour',$this->date_time->get_datetime_by_index_today($i));
				$this->slice_hash[$slice->start_time->get_mysql_date().":".$slice->start_time->hour ] = $slice;
				$this->slices_arr[] =  $slice->start_time->get_mysql_date().":".$slice->start_time->hour;
			}
		}
		else if ( $this->view == 'year')
		{

			for($i=0;$i<12;$i++)
			{
				$slice = new Slice2('month',$this->date_time->get_day_by_index_this_year($i));
				$this->slice_hash[$slice->start_time->get_mysql_date()] = $slice;
				array_push($this->slices_arr,  $slice->start_time->get_mysql_date());
			}
		}
		else
		{
			sugar_die("not a valid view:".$this->view);
		}

	}

	function add_activities($user,$type='sugar') {
		if($this->view == 'week' || $this->view == 'shared') {
			$start_date_time = $this->date_time->get_first_day_of_last_week();
			$end_date_time = $this->date_time->get_first_day_of_next_week();
		} else {
			$start_date_time = $this->date_time;
			$end_date_time = $this->date_time;
		}
		
		if($this->view == 'month'){
			$start_date_time = $this->date_time->get_first_day_of_last_month();
			$end_date_time = $this->date_time->get_first_day_of_next_month();	
		}
               
		$acts_arr = array();
		if ( $type == 'vfb') {
			$acts_arr = Calendar2Activity::get_freebusy_activities($user, $start_date_time, $end_date_time);
		} else {

                        $GLOBALS['log']->debug("in Calendar2.php user->object_name=".$user->object_name);

			//Added for RM  Dec 25 2005 by CareBrains
			if ( $user->object_name == 'User' )
			{
                               $acts_arr = Calendar2Activity::get_activities($user->id, $this->show_tasks, $start_date_time, $end_date_time, $this->view);
                               //pr("************************");
                               //pr($user->id);
                               //pr($this->show_tasks);
                               //pr($start_date_time);
                               //pr($end_date_time);
                               //pr($this->view);
                               //pr("************************");
                               
			} else {
	//END			
				$acts_arr = Calendar2Activity::get_resource_activities($user->id, $this->show_tasks, $start_date_time, $end_date_time, 'week');
			}
    	}

	    $GLOBALS['log']->debug("in add_activities count(act_arr)=".count($acts_arr));
	    // loop thru each activity for this user
		for ($i = 0;$i < count($acts_arr);$i++) {
			$act = $acts_arr[$i];
                        
	      // get "hashed" time slots for the current activity we are looping through
			$hash_list =DateTimeUtil2::getHashList($this->view,$act->start_time,$act->end_time);

			for($j=0;$j < count($hash_list); $j++) {
				if ( !isset($this->slice_hash[$hash_list[$j]]) || !isset($this->slice_hash[$hash_list[$j]]->acts_arr[$user->id])) {
					$this->slice_hash[$hash_list[$j]]->acts_arr[$user->id] = array();
				}
				$this->slice_hash[$hash_list[$j]]->acts_arr[$user->id][] = $act;
			}

//                        pr("************************");
//                        pr($user->id);
//                        pr("************************");
			$this->acts_arr2[$user->id][] = $act;
		}
	}

	function occurs_within_slice(&$slice,&$act)
	{
		// if activity starts within this slice
		// OR activity ends within this slice
		// OR activity starts before and ends after this slice
		if ( ( $act->start_time->ts >= $slice->start_time->ts &&
			 $act->start_time->ts <= $slice->end_time->ts )
			||
			( $act->end_time->ts >= $slice->start_time->ts &&
			$act->end_time->ts <= $slice->end_time->ts )
			||
			( $act->start_time->ts <= $slice->start_time->ts &&
			$act->end_time->ts >= $slice->end_time->ts )
		)
		{
			//print "act_start:{$act->start_time->ts}<BR>";
			//print "act_end:{$act->end_time->ts}<BR>";
			//print "slice_start:{$slice->start_time->ts}<BR>";
			//print "slice_end:{$slice->end_time->ts}<BR>";
			return true;
		}

		return false;

	}

	function get_previous_date_str()
	{
		if ($this->view == 'month')
		{
			$day = $this->date_time->get_first_day_of_last_month();
		}
		else if ($this->view == 'week' || $this->view == 'shared')
		{
			$day = $this->date_time->get_first_day_of_last_week();
		}
		else if ($this->view == 'day')
		{
			$day = $this->date_time->get_yesterday();
		}
		else if ($this->view == 'year')
		{
			$day = $this->date_time->get_first_day_of_last_year();
		}
		else
		{
			return "get_previous_date_str: notdefined for this view";
		}
		return $day->get_date_str();
	}

	function get_next_date_str()
	{
		if ($this->view == 'month')
		{
			$day = $this->date_time->get_first_day_of_next_month();
		}
		else
		if ($this->view == 'week' || $this->view == 'shared' )
		{
			$day = $this->date_time->get_first_day_of_next_week();
		}
		else
		if ($this->view == 'day')
		{
			$day = $this->date_time->get_tomorrow();
		}
		else
		if ($this->view == 'year')
		{
			$day = $this->date_time->get_first_day_of_next_year();
		}
		else
		{
			sugar_die("get_next_date_str: not defined for view");
		}
		return $day->get_date_str();
	}

	function get_start_slice_idx()
	{

		if ($this->isDayView())
		{
			$start_at = 8;

			for($i=0;$i < 8; $i++)
			{
				if (count($this->slice_hash[$this->slices_arr[$i]]->acts_arr) > 0)
				{
					$start_at = $i;
					break;
				}
			}
			return $start_at;
		}
		else
		{
			return 0;
		}
	}
	function get_end_slice_idx()
	{
		if ( $this->view == 'month')
		{
			return $this->date_time->days_in_month - 1;
		}
		else if ( $this->view == 'week' || $this->view == 'shared')
		{
			return 6;
		}
		else if ($this->isDayView())
		{
			$end_at = 18;

			for($i=$end_at;$i < 23; $i++)
			{
				if (count($this->slice_hash[$this->slices_arr[$i+1]]->acts_arr) > 0)
				{
					$end_at = $i + 1;
				}
			}

			return $end_at;

		}
		else
		{
			return 1;
		}
	}

}

class Slice2
{
	var $view = 'day';
	var $start_time;
	var $end_time;
	var $acts_arr = array();

	function Slice2($view,$time)
	{
		$this->view = $view;
		$this->start_time = $time;

		if ( $view == 'day')
		{
			$this->end_time = $this->start_time->get_day_end_time();
		}
		if ( $view == 'hour')
		{
			$this->end_time = $this->start_time->get_hour_end_time();
		}

	}
	function get_view()
	{
		return $this->view;
	}

}

// global to switch on the offet

$DO_USER_TIME_OFFSET = false;

class vCal2 extends vCal {

	//this is a class for resources
	function vCal2() 
	{
		parent::vCal();
	}

	// query and create the FREEBUSY lines for SugarCRM Meetings and Calls and 
        // return the string
	function create_sugar_freebusy(&$user_bean, &$start_date_time, &$end_date_time)
	{
		$str = '';
		global $DO_USER_TIME_OFFSET, $timedate;

		$DO_USER_TIME_OFFSET = true;
		// get activities.. queries Meetings and Calls
		if ($user_bean->object_name == "User") {
			$acts_arr =
			CalendarActivity::get_activities($user_bean->id,
				false,
				$start_date_time,
				$end_date_time,
				'week');
		} elseif ($user_bean->object_name == "Resource") {
			$acts_arr =
			Calendar2Activity::get_resource_activities($user_bean->id,
				false,
				$start_date_time,
				$end_date_time,
				'week');
		}

		// loop thru each activity, get start/end time in UTC, and return FREEBUSY strings
		for ($i = 0;$i < count($acts_arr);$i++)
		{
			$act =$acts_arr[$i];
			$utcFormat = 'Ymd\THi';

			//OSC bug fix #36925 starts
			//$startTimeUTC = gmdate($utcFormat, $act->start_time->ts) . "00Z";
			$start_parsed = date_parse(to_db($act->sugar_bean->date_start));
			$start_ts = mktime($start_parsed['hour'],$start_parsed['minute'],$start_parsed['second'],$start_parsed['month'],$start_parsed['day'],$start_parsed['year']);
			$startTimeUTC = date($utcFormat, $start_ts). "00Z";

			$end_ts = $start_ts + $act->sugar_bean->duration_hours * 60 * 60 + $act->sugar_bean->duration_minutes * 60;
			$endTimeUTC = date($utcFormat, $end_ts). "00Z";
			//$endTimeUTC = gmdate($utcFormat, $act->end_time->ts) . "00Z";
			//OSC bug fix ends
			
			$str .= "FREEBUSY:". $startTimeUTC ."/". $endTimeUTC."\n";
		}
		
		return $str;

	}

	// static function:
    // cache vcals
    function cache_sugar_vcal(&$user_focus)
    {
        vCal2::cache_sugar_vcal_freebusy($user_focus);
    }

	// static function:
    // caches vcal for Activities in Sugar database
    function cache_sugar_vcal_freebusy(&$user_focus)
    {
        $focus = new vCal2();
        // set freebusy members and save 
        $arr = array('user_id'=>$user_focus->id,'type'=>'vfb','source'=>'sugar');
        $focus->retrieve_by_string_fields($arr);
                                                                                                
                                                                                                
        $focus->content = $focus->get_vcal_freebusy($user_focus,false);
		$GLOBALS['log']->debug('vCal2: content='.$focus->content);
        $focus->type = 'vfb';
        $focus->date_modified = null;
        $focus->source = 'sugar';
        $focus->user_id = $user_focus->id;
        $check_notify =(!empty($_REQUEST['send_invites']) && $_REQUEST['send_invites'] == '1') ? true : false;
        $focus->save($check_notify);
    }

}

class Calendar2Activity extends CalendarActivity
{
	
	function Calendar2Activity($args)
	{
		parent::CalendarActivity($args);
    }

 	function get_resource_activities($resource_id, $show_tasks, &$view_start_time, &$view_end_time, $view) {

		$act_list = array();
		$seen_ids = array();
		
		//Meetings
		$meeting = new Meeting();
		$resource = new Resource();

		$meeting->disable_row_level_security = true;

		$where = Calendar2Activity::get_occurs_within_where_clause($meeting->table_name, $resource->rel_meetings_table, $view_start_time, $view_end_time, 'date_start', $view);
		$focus_meetings_list = Calendar2Activity::build_related_list_by_resource_id($meeting, $resource_id, $where);
		foreach($focus_meetings_list as $meeting) {
			if(isset($seen_ids[$meeting->id])) {
				continue;
			}
			
			$seen_ids[$meeting->id] = 1;
			$act = new Calendar2Activity($meeting);

			if(!empty($act)) {
				$act_list[] = $act;
			}
		}
		
		//Calls
		$call = new Call();
		$call->disable_row_level_security = true;

		$where = Calendar2Activity::get_occurs_within_where_clause($call->table_name, $resource->rel_calls_table, $view_start_time, $view_end_time, 'date_start', $view);
		$focus_calls_list = Calendar2Activity::build_related_list_by_resource_id($call, $resource_id, $where);

		foreach($focus_calls_list as $call) {
			if(isset($seen_ids[$call->id])) {
				continue;
			}
			$seen_ids[$call->id] = 1;

			$act = new Calendar2Activity($call);
			if(!empty($act)) {
				$act_list[] = $act;
			}
		}

		//Tasks
		if($show_tasks) {
				$task = new Task();
	
				$where = Calendar2Activity::get_occurs_within_where_clause('tasks', '', $view_start_time, $view_end_time, 'date_due', $view);
				$where .= " AND tasks.assigned_user_id='$user_id' ";
	
				$focus_tasks_list = $task->get_full_list("", $where,true);
	
				if(!isset($focus_tasks_list)) {
					$focus_tasks_list = array();
				}

				foreach($focus_tasks_list as $task) {
					$act = new Calendar2Activity
					($task);
					if(!empty($act)) {
						$act_list[] = $act;
					}
				}
		}

		usort($act_list,'sort_func_by_act_date');
		return $act_list;
	}

	function get_activities($user_id, $show_tasks, &$view_start_time, &$view_end_time, $view) {
		global $current_user;
		$act_list = array();
		$seen_ids = array();

                
		
		// get all upcoming meetings, tasks due, and calls for a user
		//if(ACLController::checkAccess('Meetings', 'list', $current_user->id == $user_id)) {
			//pr("3333333333333333333333333333333333333333333");
                        $meeting = new Meeting();

			//if($current_user->id  == $user_id) {
				$meeting->disable_row_level_security = true;
			//}

			$where = Calendar2Activity::get_occurs_within_where_clause($meeting->table_name, $meeting->rel_users_table, $view_start_time, $view_end_time, 'date_start', $view);
			$focus_meetings_list = build_related_list_by_user_id($meeting,$user_id,$where);
			foreach($focus_meetings_list as $meeting) {
				if(isset($seen_ids[$meeting->id])) {
					continue;
				}
				
				$seen_ids[$meeting->id] = 1;
				$act = new Calendar2Activity($meeting);
	
				if(!empty($act)) {
					$act_list[] = $act;
				}
			}
		//}
		
		//if(ACLController::checkAccess('Calls', 'list',$current_user->id  == $user_id)) {
			$call = new Call();
	
			//if($current_user->id  == $user_id) {
				$call->disable_row_level_security = true;
			//}
	
			$where = Calendar2Activity::get_occurs_within_where_clause($call->table_name, $call->rel_users_table, $view_start_time, $view_end_time, 'date_start', $view);
			$focus_calls_list = build_related_list_by_user_id($call,$user_id,$where);
	
			foreach($focus_calls_list as $call) {
				if(isset($seen_ids[$call->id])) {
					continue;
				}
				$seen_ids[$call->id] = 1;
	
				$act = new Calendar2Activity($call);
				if(!empty($act)) {
					$act_list[] = $act;
				}
			}
		//}

		if($show_tasks) {
			//if(ACLController::checkAccess('Tasks', 'list',$current_user->id == $user_id)) {
				$task = new Task();
	
				$where = Calendar2Activity::get_occurs_within_where_clause('tasks', '', $view_start_time, $view_end_time, 'date_due', $view);
				$where .= " AND tasks.assigned_user_id='$user_id' ";
	
				$focus_tasks_list = $task->get_full_list("", $where,true);
	
				if(!isset($focus_tasks_list)) {
					$focus_tasks_list = array();
				}

				foreach($focus_tasks_list as $task) {
					$act = new Calendar2Activity($task);
					if(!empty($act)) {
						$act_list[] = $act;
					}
				}
			}
		//}

		usort($act_list,'sort_func_by_act_date');
		return $act_list;
	}

	function build_related_list_by_resource_id($bean, $resource_id, $where) {

	$bean_id_name = strtolower($bean->object_name).'_id';
	$res = new Resource();
	if ($bean->object_name == "Meeting") {
		$rel_table = $res->rel_meetings_table;
	} elseif ($bean->object_name == "Call") {
		$rel_table = $res->rel_calls_table;
	} else {
		return;
	}

	$select = "SELECT {$bean->table_name}.* from {$rel_table},{$bean->table_name} ";

	$auto_where = ' WHERE ';
	if(!empty($where)) {
		$auto_where .= $where. ' AND ';
	}

	$auto_where .= " {$rel_table}.{$bean_id_name}={$bean->table_name}.id AND {$rel_table}.resource_id='{$resource_id}' AND {$bean->table_name}.deleted=0 AND {$rel_table}.deleted=0";

	if (isPro()) {
		$bean->add_team_security_where_clause($select);
	}

	$query = $select.$auto_where;

	$result = $bean->db->query($query, true);

	$list = array();

	while($row = $bean->db->fetchByAssoc($result)) {
		foreach($bean->column_fields as $field) {
			if(isset($row[$field])) {
				$bean->$field = $row[$field];
			} else {
				$bean->$field = '';
			}
		}

		$bean->processed_dates_times = array();
		$bean->check_date_relationships_load();
		$bean->fill_in_additional_detail_fields();
		
		/**
		 * PHP  5+ always treats objects as passed by reference
		 * Need to clone it if we're using 5.0+
		 * clone() not supported by 4.x
		 */
		if(version_compare(phpversion(), "5.0", ">=")) {
			$newBean = clone($bean);	
		} else {
			$newBean = $bean;
		}
		$list[] = $newBean;
	}

	return $list;
	}
}
?>
