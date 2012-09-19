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
class Calendar2Controller extends SugarController {
	
    function action_AjaxSave()
    {
		$this->view = 'ajaxsave';
    }

    function action_AjaxAfterDrop()
    {
		$this->view = 'ajaxafterdrop';
    }

    function action_AjaxGetGR()
    {
		$this->view = 'ajaxgetgr';
    }

    function action_AjaxGetGRArr()
    {
		$this->view = 'ajaxgetgrarr';
    }

    function action_AjaxLoad()
    {
		$this->view = 'ajaxload';
    }

    function action_AjaxRemove()
    {
		$this->view = 'ajaxremove';
    }
    function action_AjaxSplit()
    {
		$this->view = 'ajaxsplit';
    }
    function action_AjaxSplitSave()
    {
		$this->view = 'ajaxsplitsave';
    }
    function action_AjaxSearchContacts()
    {
		$this->view = 'ajaxsearchcontacts';
    }
    function action_AjaxFlyCreate()
    {
		$this->view = 'ajaxflycreate';
    }
    function action_AjaxEndReccurence()
    {
		$this->view = 'ajaxendreccurence';
    }
     function action_AjaxExtendReccurence()
    {
		$this->view = 'ajaxextendreccurence';
    }
    function action_AjaxGetReccurence() {
         global $current_user;
         require_once("modules/Calls/Call.php");
         
         $focus = new Call();
          switch ($_REQUEST['iSortCol_0']) {

            case 0:
                $field = "acc_number";
                break;
            case 1:
                $field = "acc_name";
                break;
            case 2:
                $field = "type_schedule";
                break;
            case 3:
                $field = "date_start";
                break;
            case 4:
                $field = "date_end";
                break;
        
            default:
                $field = "date_end";
                break;
        }
        $like_q = "";
        if(!empty($_REQUEST['sSearch'])){
            $like_q = " AND (l.account_name LIKE '%".$_REQUEST['sSearch']."%' OR a.custno_c LIKE '%".$_REQUEST['sSearch']."%' OR
l.first_name LIKE '%".$_REQUEST['sSearch']."%' OR l.last_name LIKE '%".$_REQUEST['sSearch']."%' OR a.name LIKE '%".$_REQUEST['sSearch']."%') ";
        }
         $reccurence_sql = "SELECT 
                                                    m.cal2_meeting_id_c as rec_id, 
                                                    if(m.parent_type = 'Leads',l.id,a.id) as parent_id,
                                                    if(m.parent_type = 'Leads',if(l.account_name is null,'', l.account_name),a.custno_c) as acc_number,
                                                    if(m.parent_type = 'Leads',CONCAT_WS(' ',l.first_name,l.last_name),a.name) as acc_name,
                                                    'Meeting' as type_schedule,
                                                    (SELECT meetings.date_start FROM meetings
                                                            WHERE meetings.id = m.cal2_meeting_id_c
                                                    ) as date_start,
                                                    (SELECT meetings.cal2_repeat_end_date_c FROM meetings
                                                            WHERE meetings.id = m.cal2_meeting_id_c
                                                    ) as date_end
                                                FROM meetings m
                                                    LEFT JOIN accounts a on a.id = m.parent_id
                                                    LEFT JOIN leads l on l.id = m.parent_id
                                                WHERE EXISTS (SELECT meetings.id FROM meetings
                                                                    WHERE meetings.id = m.cal2_meeting_id_c)
                                                    AND m.cal2_meeting_id_c is not NULL 
                                                    AND m.parent_id is not NULL
                                                    AND m.deleted = 0
                                                    AND m.assigned_user_id = '".$current_user->id."'
                                                    ".$like_q."
                                                GROUP BY m.cal2_meeting_id_c, m.parent_id
                                            UNION
                                            SELECT 
                                                    c.cal2_call_id_c as rec_id, 
                                                    if(c.parent_type = 'Leads',l.id,a.id) as parent_id,
                                                    if(c.parent_type = 'Leads',if(l.account_name is null,'', l.account_name),a.custno_c) as acc_number,
                                                    if(c.parent_type = 'Leads',CONCAT_WS(' ',l.first_name,l.last_name),a.name) as acc_name,
                                                    'Call' as type_schedule,
                                                    (SELECT calls.date_start FROM calls
                                                            WHERE calls.id = c.cal2_call_id_c
                                                    ) as date_start,
                                                    (SELECT calls.cal2_repeat_end_date_c FROM calls
                                                            WHERE calls.id = c.cal2_call_id_c
                                                    ) as date_end
                                                FROM calls c
                                                    LEFT JOIN accounts a on a.id = c.parent_id
                                                    LEFT JOIN leads l on l.id = c.parent_id
                                                WHERE EXISTS (SELECT calls.id FROM calls
                                                                    WHERE calls.id = c.cal2_call_id_c)
                                                    AND c.cal2_call_id_c is not NULL 
                                                    AND c.parent_id is not NULL
                                                    AND c.deleted = 0
                                                    AND c.assigned_user_id = '".$current_user->id."'
                                                    ".$like_q."
                                                GROUP BY c.cal2_call_id_c, c.parent_id
                                            ORDER BY " . $field . " " . $_REQUEST['sSortDir_0'] . " LIMIT " . $_GET['iDisplayStart'] . ", " . $_GET['iDisplayLength'];

        $meetings_sql_count = "SELECT count(counter) AS meetings_records  FROM (select 
                                                                count(m.cal2_meeting_id_c) as counter
                                                                from meetings m
                                                                left join accounts a on a.id = m.parent_id
                                                                left join leads l on l.id = m.parent_id
                                                                where EXISTS (SELECT meetings.id FROM meetings
                                                                    WHERE meetings.id = m.cal2_meeting_id_c)
                                                                and m.cal2_meeting_id_c is not NULL 
                                                                and m.parent_id is not NULL
                                                                and m.deleted = 0
                                                                and m.assigned_user_id = '".$current_user->id."'
                                                                ".$like_q."
                                                                GROUP BY m.cal2_meeting_id_c, m.parent_id) countMeetings";
        $calls_sql_count = "SELECT count(counter) AS calls_records  FROM (select  
                                                                count(c.cal2_call_id_c) as counter
                                                                from calls c
                                                                left join accounts a on a.id = c.parent_id
                                                                left join leads l on l.id = c.parent_id
                                                                where EXISTS (SELECT calls.id FROM calls
                                                                    WHERE calls.id = c.cal2_call_id_c)
                                                                and c.cal2_call_id_c is not NULL 
                                                                and c.parent_id is not NULL
                                                                and c.deleted = 0
                                                                and c.assigned_user_id = '".$current_user->id."'
                                                                ".$like_q."
                                                                GROUP BY c.cal2_call_id_c, c.parent_id) countCalls";
        $reccurence = $focus->db->query($reccurence_sql);
        $meetings_count = $focus->db->query($meetings_sql_count);
        $calls_count = $focus->db->query($calls_sql_count);
        
        $meet_count = $focus->db->fetchByAssoc($meetings_count);
        $call_count = $focus->db->fetchByAssoc($calls_count);
        $totalRecords = $meet_count['meetings_records'] + $call_count['calls_records'];
        $recc_ids = array();
        while ($recc = $focus->db->fetchByAssoc($reccurence)) {
            $recc_ids[] = array(
                $recc['acc_number'],
                $recc['acc_name'],
                '<span id="' . $recc['rec_id'] . '_type_schedule">'.$recc['type_schedule'].'</span>',
                '<span id="' . $recc['rec_id'] . '_start_date">'.date("m/d/Y", strtotime($recc['date_start'])).'</span>',
                '<span id="' . $recc['rec_id'] . '_end_date">'.date("m/d/Y", strtotime($recc['date_end'])).'</span>',
                '<input autocomplete="off" name="' . $recc['rec_id'] . '_end_reccurence" id="' . $recc['rec_id'] . '_end_reccurence" class="end_reccurence" rec_id="' . $recc['rec_id'] . '" value="" title="" tabindex="105" size="11" maxlength="10" type="text">',
                '<input autocomplete="off" name="' . $recc['rec_id'] . '_extend_reccurence" id="' . $recc['rec_id'] . '_extend_reccurence" class="extend_reccurence" rec_id="' . $recc['rec_id'] . '" value="" title="" tabindex="105" size="11" maxlength="10" type="text">',
            );
        }

        print json_encode(array("sEcho" => $_GET["sEcho"], "iTotalRecords" => $totalRecords, "iTotalDisplayRecords" => $totalRecords, "aaData" => $recc_ids));
        exit;
    }
}
?>