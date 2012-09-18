<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function getPreviousMeeting(&$focus, $field, $value, $view)
{
    //include_once 'modules/Calendar2/functions.php';
    

    global $db;
    $val = '';

    global $timedate;
        if (!$timedate) {
            $timedate = new TimeDate();
        }


    $date_start = $timedate->to_db($focus->date_start);
    $start_for_previous = strtotime($date_start);


//    $dbf = $timedate->get_db_date_time_format();
//    $timezone = $GLOBALS['timedate']->getUserTimeZone();
//    $date = date($dbf,strtotime($focus->date_start)-$timezone['gmtOffset']*60);

    $type = $focus->parent_type;
    $id = $focus->parent_id;
     
   if ($id == '') return $val = ' ';


   if($type == 'Accounts' || $type == 'Leads' )
       {
        $q = ''
            . 'SELECT '
            . 'outcome_c, date_start, status  '
            . 'FROM meetings '
            . 'WHERE parent_id = \'' . $id . '\' '
            . ' AND UNIX_TIMESTAMP(date_start) < \''.$start_for_previous.'\''
            . 'ORDER BY UNIX_TIMESTAMP(date_start)DESC LIMIT 1';
        $rs = $db->query($q);

   

        while($row = $db->fetchByAssoc($rs)) {
            $val =$row['outcome_c'].' ('.$timedate->to_display_date_time($row['date_start']).')'.' Status: '.$row['status'];
           }
           if ($val == "") $val = "No previous meeting or call held for this customer or lead";

       }


   
    if ($view == 'DetailView') {
        return $val;
    }

    if ($view == 'EditView') {
        return $val;
    }

}