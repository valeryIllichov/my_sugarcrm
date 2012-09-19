<?php

/**
 * Calendar2ViewAjaxExtendReccurence
 *
 * */
require_once('include/MVC/View/SugarView.php');
require_once('modules/Calendar2/Calendar2.php');

class Calendar2ViewAjaxExtendReccurence extends SugarView {

    function Calendar2ViewAjaxExtendReccurence() {
        parent::SugarView();
    }

    function process() {
        $this->display();
    }

    function display() {
        require_once("modules/Calls/Call.php");
        require_once("modules/Meetings/Meeting.php");
        if(!empty($_POST['jsonObj'])){
            $post_obj = json_decode(htmlspecialchars_decode(stripslashes($_POST['jsonObj'])));
        } elseif (!empty($_REQUEST['record'])&&!empty($_REQUEST['cal2_repeat_end_date_c'])) {
            $post_obj = new StdClass();
            if ($_REQUEST['cur_module'] == 'Calls') {
                $post_obj->Call[$_REQUEST['record']] = $_REQUEST['cal2_repeat_end_date_c'];
            }
            if ($_REQUEST['cur_module'] == 'Meetings') {
                $post_obj->Meeting[$_REQUEST['record']] = $_REQUEST['cal2_repeat_end_date_c'];
            }
        }
        if (isset($post_obj->Call)) {
            $jn = "cal2_call_id_c";
            $type = 'call';
            foreach ($post_obj->Call as $rec_id => $end_date) {
                $call_bean = new Call();
                $call_bean->retrieve($rec_id);
                $start_date = $this->getStartDate($call_bean, $jn, $rec_id,$call_bean->cal2_repeat_end_date_c);
                $start_date = $start_date." ".date('h:ia',strtotime($call_bean->date_start));
                $call_bean->cal2_repeat_end_date_c = $end_date;
                $call_bean->save();
                $this->extendRecurrence($call_bean, $jn, $type, $start_date);
            }
        }
        if (isset($post_obj->Meeting)) {
            $jn = "cal2_meeting_id_c";
            $type = 'meeting';
            foreach ($post_obj->Meeting as $rec_id => $end_date) {
                $meeting_bean = new Meeting();
                $meeting_bean->retrieve($rec_id);
                $start_date = $this->getStartDate($meeting_bean, $jn, $rec_id,$meeting_bean->cal2_repeat_end_date_c);
                $start_date = $start_date." ".date('h:ia',strtotime($meeting_bean->date_start));
                $meeting_bean->cal2_repeat_end_date_c = $end_date;
                $meeting_bean->save();
                $this->extendRecurrence($meeting_bean, $jn, $type, $start_date);
            }
        }
    }

    function extendRecurrence(&$bean, $jn, $type, $start_date) {
        require_once("modules/Calendar2/functions.php");

        $user_ids = array();
        if(isset($bean->resources_arr)&&  is_array($bean->resources_arr)){
            foreach ($bean->resources_arr as $r)
                $user_ids[] = $r;
        }
        $arr_rec = array();
        $arr_rec = $this->createRecurrence($bean, $jn, $start_date);
        if (!empty($_REQUEST['record'])&&!empty($_REQUEST['clicked_record'])) {
            $bean->retrieve($_REQUEST['clicked_record']);
            
            //$bean->retrieve($bean->id); // do not delete this line!!! it prevents the sugar's bug with timedate!

            global $timedate;

            if ($type == 'call')
                $users = $bean->get_call_users();
            if ($type == 'meeting')
                $users = $bean->get_meeting_users();
            //store user ids to update schedulerrows on browser
            //$user_ids = array();
            foreach ($users as $u)
                $user_ids[] = $u->id;

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
            $repeat_type = (isset($bean->cal2_repeat_type_c)) ? $bean->cal2_repeat_type_c : "";
            $repeat_interval = (isset($bean->cal2_repeat_interval_c)) ? $bean->cal2_repeat_interval_c : "";
            $repeat_end_date = (isset($bean->cal2_repeat_end_date_c)) ? $bean->cal2_repeat_end_date_c : "";
            $repeat_days = (isset($bean->cal2_repeat_days_c)) ? $bean->cal2_repeat_days_c : "";
            $recur_id = (isset($_REQUEST['record'])) ? $_REQUEST['record'] : "";

            $custno_c = '';
            $customer_name = '';
            $lead_name = '';

            $parent_type = strtolower($bean->parent_type);

            if ($parent_type == 'accounts') {
                global $moduleList, $beanList, $beanFiles;
                $class_name = $beanList['Accounts'];
                $class_file_path = $beanFiles[$class_name];
                require_once $class_file_path;
                $o = new $class_name();
                $o->retrieve($bean->parent_id);

                $custno_c = (string) $o->custno_c;
                $customer_name = (string) $o->name;
            }

            if ($parent_type == 'leads') {
                global $moduleList, $beanList, $beanFiles;
                $class_name = $beanList['Leads'];
                $class_file_path = $beanFiles[$class_name];
                require_once $class_file_path;
                $o = new $class_name();
                $o->retrieve($bean->parent_id);

                $lead_name = (string) $o->name;
                $lead_account_name = (string) $o->account_name;
            }

            require_once 'modules/Calendar2/class.Calendar2_WidgetTitle.php';
            $widget_title = Calendar2_WidgetTitle::create(
                            $bean->parent_type, $bean->parent_id, $customer_name, $lead_name, $bean->name, $bean->status, $lead_account_name, $custno_c
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
                'time_start' => timestamp_to_user_formated2($start, $GLOBALS['timedate']->get_time_format()),
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
                'widget_title' => $widget_title
            );

            echo json_encode($json_arr);
        }
    }

    function createRecurrence(&$bean, $jn, $start) {

        require_once("modules/Calendar2/functions.php");

        $ret_arr = array();

        $repeat_days = $bean->cal2_repeat_days_c;
        $rtype = $bean->cal2_repeat_type_c;
        if (!empty($bean->cal2_repeat_interval_c)) {
            $interval = $bean->cal2_repeat_interval_c;
        } else {
            $interval = 0;
        }

        $timezone = $GLOBALS['timedate']->getUserTimeZone();
        global $timedate;

        $start_unix = to_timestamp_from_uf($start);
        $start_unix = $start_unix;
        $end = date("m/d/Y", strtotime($bean->cal2_repeat_end_date_c));
        $end_unix = to_timestamp_from_uf($end);
        $end_unix = $end_unix + 60 * 60 * 24 - 1;

        $start_day = date("w", $start_unix - date('Z', $start_unix)) + 1;
        $start_dayM = date("j", $start_unix - date('Z', $start_unix));

        if (!empty($rtype) && (!isset($bean->cal2_repeat_end_date_c) || !empty($bean->cal2_repeat_end_date_c))) {

            if (empty($interval) || $interval == 0)
                $interval = 1;

            $cur_date = $start_unix;

            $i = 0;
            if ($rtype == 'Weekly' || $rtype == 'Monthly (day)')
                $i--;

            while ($cur_date <= $end_unix) {

                $i++;

                if ($rtype == 'Daily')
                    $step = 60 * 60 * 24;
                if ($rtype == 'Monthly (date)') {
                    $step = 60 * 60 * 24 * date('t', $cur_date - date('Z', $cur_date));
                    $this_month = date('n', $cur_date - date('Z', $cur_date));
                    $next_month = date('n', $cur_date + $step - date('Z', $cur_date));
                    if ($next_month - $this_month == 2 || $next_month - $this_month == -10) {
                        $day_number = intval(date('d', $cur_date + $step - date('Z', $cur_date))) + 1;
                        $step += 60 * 60 * 24 * date('t', $cur_date + $step - $day_number * 24 * 3600 - date('Z', $cur_date));
                        $i++;
                    }
                }
                if ($rtype == 'Yearly') {
                    $step = 60 * 60 * 24 * 365;
                    if (date('d', $cur_date + $step - date('Z', $cur_date)) != date('d', $cur_date - date('Z', $cur_date)))
                        $step += 60 * 60 * 24;
                }

                if ($rtype == 'Weekly') {
                    $step = 60 * 60 * 24 * 7;
                    //sunday of the week
                    $week_start_day = $start_unix - ($start_day - 1) * 60 * 60 * 24 + $step * $i;

                    //if($i % $interval == 0)
                    if ($i > 0 && $i % $interval == 0)
                    //for($d = $start_day; $d < 7; $d++)
                        for ($d = 1; $d < 8; $d++)
                        //if(strpos($repeat_days,(string)($d + 1)) !== false){
                            if (strpos($repeat_days, (string) ($d)) !== false) {
                                //if($cur_date + $d*60*60*24 > $end_unix)
                                if ($week_start_day + ($d - 1) * 60 * 60 * 24 > $end_unix)
                                    break;
                                //$ret_arr[] = $this->create_clone($bean,$cur_date + ($d)*60*60*24,$jn);
                                $ret_arr[] = $this->create_clone($bean, $week_start_day + ($d - 1) * 60 * 60 * 24, $jn);
                            }
                    //$start_day = 0;
                }

                if ($rtype == 'Monthly (day)') {
                    $step = 60 * 60 * 24 * date('t', $cur_date - date('Z', $cur_date));

                    if ($i % $interval == 0 && $start_dayM <= $start_day)
                        for ($d = $start_day; $d < 7; $d++) {
                            $dd = date('w', $cur_date + $d * 60 * 60 * 24 - date('Z', $cur_date));
                            if (strpos($repeat_days, (string) ($dd + 1)) !== false) {
                                //$ST_curr = getDST($cur_date + $d*60*60*24);
                                //$cur_date += ($ST_prev - $ST_curr)*3600;
                                //$ST_prev = getDST($cur_date + $d*60*60*24);
                                if ($cur_date + $d * 60 * 60 * 24 > $end_unix)
                                    break;
                                $ret_arr[] = $this->create_clone($bean, $cur_date + $d * 60 * 60 * 24, $jn);
                            }
                        }
                    $start_day = 0;
                    $start_dayM = 0;
                }

                $cur_date += $step;

                //$ST_curr = getDST($cur_date);
                //$cur_date += ($ST_prev - $ST_curr)*3600;
                //$ST_prev = getDST($cur_date);

                if ($i % $interval != 0)
                    continue;

                if ($cur_date > $end_unix)
                    break;

                if ($rtype == 'Weekly' || $rtype == 'Monthly (day)')
                    continue;

                $ret_arr[] = $this->create_clone($bean, $cur_date, $jn);
            }
        }

        return $ret_arr;
    }

    function create_clone(&$bean, $cur_date, $jn) {

        $obj = $this->clone_rec($bean);
        $obj->date_start = timestamp_to_user_formated2($cur_date);
        $obj->date_end = timestamp_to_user_formated2($cur_date);
        $obj->$jn = $bean->id;
        $obj->save();
        $obj_id = $obj->id;

        $obj->retrieve($obj_id);

        $date_unix = $cur_date;
        return array(
            'record' => $obj_id,
            'start' => $date_unix,
        );
    }

    function clone_rec($bean) {
        global $current_user;
        $obj = new $bean->object_name();
        $obj->name = $bean->name;
        $obj->duration_hours = $bean->duration_hours;
        $obj->duration_minutes = $bean->duration_minutes;
        $obj->reminder_time = $bean->reminder_time;
      //  if ($obj->object_name == 'Call')
       //     $obj->direction = $bean->direction;
        //$obj->status = $bean->status;
        //$obj->location = $bean->location;
        $obj->assigned_user_id = $current_user->id;
        $obj->parent_type = $bean->parent_type;
        $obj->parent_id = $bean->parent_id;
        //$obj->description = $bean->description;
        $obj->cal2_category_c = "customerin";
        //$obj->cal2_options_c = $bean->cal2_options_c;
        //$obj->cal2_whole_day_c = $bean->cal2_whole_day_c;

//        if (isPro()) {
//            $obj->team_id = $bean->team_id;
//            if (is551()) {
//                $obj->team_set_id = $bean->team_set_id;
//            }
//        }

        return $obj;
    }
    
    function getStartDate(&$bean, $jn, $rec_id,$start_date){
        $rec_sql = "SELECT t.date_start as date_start
            FROM ".$bean->table_name." t
            WHERE t.".$jn." = '".addslashes($rec_id)."' AND t.deleted = 0
            ORDER BY t.date_start DESC";
        $result = $bean->db->query($rec_sql);
        $rec = $bean->db->fetchByAssoc($result);
        if(!empty($rec) && strtotime($rec['date_start']))
            $start_date =  date("m/d/Y", strtotime($rec['date_start']));
 
        return $start_date;
    }

}

