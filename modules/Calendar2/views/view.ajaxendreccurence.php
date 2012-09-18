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
        
        $post_obj =  json_decode(htmlspecialchars_decode(stripslashes($_POST['jsonObj'])));
        if(isset($post_obj->Call)){
            $call_bean = new Call();
            foreach ($post_obj->Call as $rec_id=>$end_date){
                    $deleteUpdate = "UPDATE ".$call_bean->table_name." c
                                             SET c.deleted = 1
                                             WHERE c.cal2_call_id_c = '".addslashes($rec_id)."' 
                                             AND c.date_end >= STR_TO_DATE('".$end_date."', '%m/%d/%Y')";
	$call_bean->db->query($deleteUpdate);
                  $dateUpdate = "UPDATE ".$call_bean->table_name." c
                                             SET c.cal2_repeat_end_date_c = STR_TO_DATE('".$end_date."', '%m/%d/%Y')
                                             WHERE c.id = '".addslashes($rec_id)."'";
	$call_bean->db->query($dateUpdate);
            }
        }
        if(isset($post_obj->Meeting)){
            $meeting_bean = new Meeting();
            foreach ($post_obj->Meeting as $rec_id=>$end_date){
                    $deleteUpdate = "UPDATE ".$meeting_bean->table_name." m
                                             SET m.deleted = 1
                                             WHERE m.cal2_meeting_id_c = '".addslashes($rec_id)."' 
                                             AND m.date_end >= STR_TO_DATE('".$end_date."', '%m/%d/%Y')";
	$meeting_bean->db->query($deleteUpdate);
                  $dateUpdate = "UPDATE ".$meeting_bean->table_name." m
                                             SET m.cal2_repeat_end_date_c = STR_TO_DATE('".$end_date."', '%m/%d/%Y')
                                             WHERE m.id = '".addslashes($rec_id)."'";
	$meeting_bean->db->query($dateUpdate);
            }
        }    

    }

}
