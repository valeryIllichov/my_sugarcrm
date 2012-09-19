<?php

/**
 * Calendar2ViewAjaxEndReccurence
 *
 * */
require_once('include/MVC/View/SugarView.php');
require_once('modules/Calendar2/Calendar2.php');

class Calendar2ViewAjaxEndReccurence extends SugarView {

    function Calendar2ViewAjaxEndReccurence() {
        parent::SugarView();
    }

    function process() {
        $this->display();
    }

    function display() {
        require_once("modules/Calls/Call.php");
        require_once("modules/Meetings/Meeting.php");
        require_once("modules/Calendar2/functions.php");
        if(!empty($_POST['jsonObj'])){
            $post_obj = json_decode(htmlspecialchars_decode(stripslashes($_POST['jsonObj'])));
        } elseif (!empty($_REQUEST['record'])&&!empty($_REQUEST['clicked_record'])) {
            $post_obj = new StdClass();
            if ($_REQUEST['cur_module'] == 'Calls') {
                $clicked_bean = new Call();
                $clicked_bean->retrieve($_REQUEST['clicked_record']);
                $post_obj->Call[$_REQUEST['record']] =$clicked_bean->date_start;
            }
            if ($_REQUEST['cur_module'] == 'Meetings') {
                $clicked_bean = new Meeting();
                $clicked_bean->retrieve($_REQUEST['clicked_record']);
                $post_obj->Meeting[$_REQUEST['record']] = $clicked_bean->date_start;
            }
        }
        if(isset($post_obj->Call)){
            $call_bean = new Call();
            foreach ($post_obj->Call as $rec_id=>$end_date){
                $deleted_ids= $this->loadRecords($call_bean,'cal2_call_id_c',$rec_id,$end_date);
                    $deleteUpdate = "UPDATE ".$call_bean->table_name." c
                                             SET c.deleted = 1, c.date_modified = '".gmdate($GLOBALS['timedate']->get_db_date_time_format())."'
                                             WHERE c.cal2_call_id_c = '".addslashes($rec_id)."' AND c.deleted = 0
                                             AND c.date_end >= STR_TO_DATE('".$end_date."', '%m/%d/%Y')";
	$call_bean->db->query($deleteUpdate);
                  $dateUpdate = "UPDATE ".$call_bean->table_name." c
                                             SET c.cal2_repeat_end_date_c = STR_TO_DATE('".date("m/d/Y",to_timestamp_from_uf($end_date) - 60*60*24)."', '%m/%d/%Y'), 
                                                     c.date_modified = '".gmdate($GLOBALS['timedate']->get_db_date_time_format())."'
                                             WHERE c.id = '".addslashes($rec_id)."'";
	$call_bean->db->query($dateUpdate);
                $this->returnJson($call_bean, 'call',$deleted_ids);
            }
        }
        if(isset($post_obj->Meeting)){
            $meeting_bean = new Meeting();
            foreach ($post_obj->Meeting as $rec_id=>$end_date){
                   $deleted_ids= $this->loadRecords($meeting_bean,'cal2_meeting_id_c',$rec_id,$end_date);
                   $deleteUpdate = "UPDATE ".$meeting_bean->table_name." m
                                             SET m.deleted = 1, m.date_modified = '".gmdate($GLOBALS['timedate']->get_db_date_time_format())."'
                                             WHERE m.cal2_meeting_id_c = '".addslashes($rec_id)."' AND m.deleted = 0
                                             AND m.date_end >= STR_TO_DATE('".$end_date."', '%m/%d/%Y')";
	$meeting_bean->db->query($deleteUpdate);
                  $dateUpdate = "UPDATE ".$meeting_bean->table_name." m
                                             SET m.cal2_repeat_end_date_c = STR_TO_DATE('".date("m/d/Y",to_timestamp_from_uf($end_date) - 60*60*24)."', '%m/%d/%Y'),
                                                    m.date_modified = '".gmdate($GLOBALS['timedate']->get_db_date_time_format())."'
                                             WHERE m.id = '".addslashes($rec_id)."'";
	$meeting_bean->db->query($dateUpdate);
                  $this->returnJson($meeting_bean, 'meeting',$deleted_ids);
            }
        }    

    }
    
    function returnJson(&$bean, $type,$deleted_ids) {   
        if (!empty($_REQUEST['record'])&&!empty($_REQUEST['clicked_record'])) {
              require_once("modules/Calendar2/functions.php");

            $user_ids = array();
            if(isset($bean->resources_arr)&&  is_array($bean->resources_arr)){
                foreach ($bean->resources_arr as $r)
                    $user_ids[] = $r;
            }
            
            $bean->retrieve($_REQUEST['clicked_record']);

            if ($type == 'call')
                $users = $bean->get_call_users();
            if ($type == 'meeting')
                $users = $bean->get_meeting_users();

            foreach ($users as $u)
                $user_ids[] = $u->id;

        
            $json_arr = array(
                'succuss' => 'yes',
                'users' => $user_ids,
                'deleted_ids' => $deleted_ids
            );

            echo json_encode($json_arr);
        }
    }
    
    function loadRecords(&$bean,$jn,$rec_id,$end_date){
        $ret_arr = array();
         if (!empty($_REQUEST['record'])&&!empty($_REQUEST['clicked_record'])) {
            $rec_sql = "SELECT t.id as record_id
                FROM ".$bean->table_name." t
                WHERE t.".$jn." = '".addslashes($rec_id)."' AND t.deleted = 0
                AND t.date_end >= STR_TO_DATE('".$end_date."', '%m/%d/%Y')";
            $result = $bean->db->query($rec_sql);
            while ($rec = $bean->db->fetchByAssoc($result)) {
                $ret_arr[] =  $rec['record_id'];
            }
         }
        return $ret_arr;
    }
}
