<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');


require_once 'include/QuickSearchDefaults.php';
$qsd = new QuickSearchDefaults();

//pr($qsd->getQSUser());

$sqs_objects['cal2_assigned_user_name'] = $qsd->getQSUser();

$o = $qsd->getQSParent();
$o['field_list'] = array('name', 'custno_c', 'id');
$o['populate_list'] = array("parent_name", 'parent_name_custno_c', "parent_id");
$o['field_list'][] = array('custno_c','account_name');
$o['populate_list'][] = 'lead_account_name';
$sqs_objects['parent_name'] = $o;

$o = $qsd->getQSParent();
$o['field_list'] = array('custno_c', 'name', 'id');
$o['populate_list'] = array('parent_name_custno_c', "parent_name", "parent_id");
$o['conditions'][0]['name'] = 'custno_c';
$o['order'] = 'custno_c';

$sqs_objects['parent_name_custno_c'] = $o;
$sqs_objects['cal2_assigned_user_name'] = $qsd->getQSUser();

$o = $qsd->getQSParent();
$o['field_list'] = array('account_name', 'name', 'id');
$o['populate_list'] = array('lead_account_name', "parent_name", "parent_id");
$o['conditions'][0]['name'] = 'account_name';
$o['order'] = 'account_name';
$o['modules'][0] = 'Leads';

$sqs_objects['lead_account_name'] = $o;
$sqs_objects['cal2_assigned_user_name'] = $qsd->getQSUser();

$quicksearch_js = '<script language="javascript">';
$quicksearch_js.= "if(typeof sqs_objects == 'undefined'){var sqs_objects = new Array;}";
$json = getJSONobj();
foreach ($sqs_objects as $sqsfield => $sqsfieldArray) {
    $quicksearch_js .= "sqs_objects['$sqsfield']={$json->encode($sqsfieldArray)};";
}
$quicksearch_js .= '</script>';

/* * ****************************************************************************
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

 * The COMPANY may not copy, deliver, distribute the SOFTWARE without written
  permit from OpensourceCRM.
 * The COMPANY may not reverse engineer, decompile, or disassemble the 
  SOFTWARE, except and only to the extent that such activity is expressly
  permitted by applicable law notwithstanding this limitation.
 * The COMPANY may not sell, rent, or lease resell, or otherwise transfer for
  value, the SOFTWARE.
 * Termination. Without prejudice to any other rights, OpensourceCRM may 
  terminate this Agreement if the COMPANY fail to comply with the terms and
  conditions of this Agreement. In such event, the COMPANY must destroy all
  copies of the SOFTWARE and all of its component parts.
 * OpensourceCRM will give the COMPANY notice and 30 days to correct above 
  before the contract will be terminated.

  The SOFTWARE is protected by copyright and other intellectual property laws and
  treaties. OpensourceCRM owns the title, copyright, and other intellectual
  property rights in the SOFTWARE.
 * *************************************************************************** */

function Call_Meeting_output() {


    require_once("include/utils.php");
    global $current_language;
    global $current_user;
    $calls_lang = return_module_language($current_language, "Calls");
    $meetings_lang = return_module_language($current_language, "Meetings");



    global $app_strings, $app_list_strings, $beanList;
    global $timedate;
    $gmt_default_date_start = $timedate->get_gmt_db_datetime();
    $user_default_date_start = $timedate->handle_offset($gmt_default_date_start, $GLOBALS['timedate']->get_date_time_format());

//$CALENDAR_DATEFORMAT = $timedate->get_cal_date_format());
//$USER_DATEFORMAT = $timedate->get_user_date_format());
    $date_format = $timedate->get_cal_date_format();
    $time_format = $timedate->get_user_time_format();
    $TIME_FORMAT = $time_format;
    $t23 = strpos($time_format, '23') !== false ? '%H' : '%I';
    $time_separator = strpos($time_format, ':') !== false ? ':' : '.';
    if (!isset($match[2]) || $match[2] == '') {
        $CALENDAR_FORMAT = $date_format . ' ' . $t23 . $time_separator . "%M";
    } else {
        $pm = $match[2] == "pm" ? "%P" : "%p";
        $CALENDAR_FORMAT = $date_format . ' ' . $t23 . $time_separator . "%M" . $pm;
    }


    $hours_arr = array();
    $num_of_hours = 24;
    $start_at = 0;

    $TIME_MERIDIEM = "";
    $time_pref = $timedate->get_time_format();
    if (strpos($time_pref, 'a') || strpos($time_pref, 'A')) {
        $num_of_hours = 13;
        $start_at = 1;

        $options = strpos($time_pref, 'a') ? $app_list_strings['dom_meridiem_lowercase'] : $app_list_strings['dom_meridiem_uppercase'];
        $TIME_MERIDIEM = get_select_options_with_id($options, 'am');

        $TIME_MERIDIEM = "<select name='date_start_meridiem' tabindex='2'>" . $TIME_MERIDIEM . "</select>";
    }

    for ($i = $start_at; $i < $num_of_hours; $i++) {
        $i = $i . "";
        if (strlen($i) == 1) {
            $i = "0" . $i;
        }
        $hours_arr[$i] = $i;
    }
    $TIME_START_HOUR_OPTIONS = get_select_options_with_id($hours_arr, 1);
    $min_options = array('0' => '00', '15' => '15', '30' => '30', '45' => '45');

//$TIME_START_MINUTE_OPTIONS = get_select_options_with_id($focus->minutes_values, 0);
    $TIME_START_MINUTES_OPTIONS = get_select_options_with_id($min_options, 0);

    $reminder_t = $current_user->getPreference('reminder_time');
    $reminderHTML = '<select name="reminder_time">';
    $reminderHTML .= get_select_options_with_id($app_list_strings['reminder_time_options'], $reminder_t);
    $reminderHTML .= '</select>';

    $CallMeetingForm = '<form id="EditView" class="sendCallMeeting2Calendar">
<input name="return_module" id="return_module" type="hidden" value="Meetings">
<input name="cur_module" id="cur_module" type="hidden" value="Meetings">
<input name="record" id="form_record" type="hidden" value="">

<input type="hidden" name="user_invitees">
<input type="hidden" name="resources_assigned">

<input type="hidden" name="cal2_repeat_type_c" id="cal2_repeat_type_c">
<input type="hidden" name="cal2_repeat_interval_c" id="cal2_repeat_interval_c">
<input type="hidden" name="cal2_repeat_end_date_c" id="cal2_repeat_end_date_c">
<input type="hidden" name="cal2_repeat_days_c" id="cal2_repeat_days_c">


<input type="hidden" name="edit_all_recurrence" id="edit_all_recurrence">

<input type="hidden" name="cal2_recur_id_c" id="cal2_recur_id_c" value="">

<input type="hidden" name="clicked_record" id="clicked_record" value="">
<input type="hidden" name="clicked_cal2_recur_id_c" id="clicked_cal2_recur_id_c" value="">
<style>
#EditView_parent_name_results { float: left; }
#EditView_assigned_user_name_results { float: left; }
</style>
<table class="edit view" cellspacing="1" cellpadding="0" border="0" width="100%">
	<tr>
		<td   width="20%" valign="top" scope="row">
			' . $calls_lang['LBL_SUBJECT'] . '			
			<span class="required">*</span>
		</td>
		<td  valign="top">
				<input id="name" type="text" tabindex="10" title="" value="" maxlength="" size="30" name="name"/>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<span>
				
		
				<input type="radio" id="radio_meeting" onchange="if(this.checked){this.form.cur_module.value=\'Meetings\'; this.form.return_module.value=\'Meetings\'; this.form.direction.style.display=\'none\'; /*GR_update_focus(\'Meetings\',\'\');*/}" checked="true"  name="appttype" />
				' . $calls_lang['LNK_NEW_MEETING'] . '
			
				
				<input type="radio" id="radio_call" onchange="if(this.checked){this.form.cur_module.value=\'Calls\'; this.form.return_module.value=\'Calls\'; this.form.direction.style.display = \'inline\'; /*GR_update_focus(\'Calls\',\'\');*/}" name="appttype" />
				
				 ' . $calls_lang['LNK_NEW_CALL'] . '
				
				
				</span>
			
		</td>
	</tr>
    
    <tr>
        <!-- <td width="20%" valign="top" scope="row">
             ' . $app_strings['LBL_LIST_RELATED_TO'] . ':
        </td> -->   
        <td valign="top">';
    if ($_REQUEST['module'] == 'Accounts') {
        $CallMeetingForm .= '
        <input name="parent_id" id="parent_id" value="" type="hidden">
        <input name="parent_name" id="parent_name" value="" type="hidden">
        <input name="parent_type" id="parent_type" value="account" type="hidden">
        ';
    } else if ($_REQUEST['module'] == 'Leads') {
        $CallMeetingForm .= '
        <input name="parent_id" id="parent_id" value="" type="hidden">
        <input name="lead_account_name" id="lead_account_name" value="" type="hidden">
        <input name="parent_type" id="parent_type" value="account" type="hidden">
        ';
    }

    $parent_types = $app_list_strings['record_type_display'];
    $disabled_parent_types = ACLController::disabledModuleList($parent_types, false, 'list');
    foreach ($disabled_parent_types as $disabled_parent_type) {
        if ($disabled_parent_type != $focus->parent_type) {
            unset($parent_types[$disabled_parent_type]);
        }
    }

    //$CallMeetingForm .= '<select name="parent_type" id="parent_type" title="" onchange=\'document.EditView.parent_name.value="";document.EditView.parent_id.value="";document.EditView.parent_name_custno_c.value=""; document.EditView.lead_account_name.value=""; changeQS(); checkParentType(document.EditView.parent_type.value, document.EditView.btn_parent_name);\'>';
//    foreach ($app_list_strings['parent_type_display'] as $k => $v)
//        $CallMeetingForm .= '<option label="' . $v . '" value="' . $k . '">' . $v . '</option>';

    /*
     * </select>
      <input name="parent_name_custno_c" class="sqsEnabled yui-ac-input" id="parent_name_custno_c" size="6" value="" autocomplete="off" type="text" tabindex="20" style="display: inline;"/>
      <input name="lead_account_name" class="sqsEnabled yui-ac-input" id="lead_account_name" size="" value="" autocomplete="off" type="text" tabindex="20" style="display: none;"/>
      <input name="parent_name" id="parent_name" class="sqsEnabled yui-ac-input" size="20" value="" autocomplete="off" type="text" tabindex="20"/>
      <div class="yui-ac-container" id="EditView_parent_name_results"><div style="display: none;" class="yui-ac-content"><div style="display: none;" class="yui-ac-hd"></div><div class="yui-ac-bd"><ul><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li></ul></div><div style="display: none;" class="yui-ac-ft"></div></div></div>
      <input name="parent_id" id="parent_id" value="" type="hidden">
      <input name="btn_parent_name" id="btn_parent_name" title="' . $app_strings['LBL_SELECT_BUTTON_TITLE'] . '" accesskey="T" class="button" value="Search" onclick=\'open_popup(document.EditView.parent_type.value, 800, 600, "", true, false, {"call_back_function":"set_return","form_name":"EditView","field_to_name_array":{"id":"parent_id","name":"parent_name","custno_c":"parent_name_custno_c", "account_name":"lead_account_name"}}, "single", true);\' type="button">

      <input name="btn_clr_parent_name" id="btn_clr_parent_name" title="' . $app_strings['LBL_CLEAR_BUTTON_TITLE'] . '" accesskey="C" class="button" onclick="this.form.parent_name.value = \'\'; this.form.parent_id.value = \'\'; this.form.parent_name_custno_c.value = \'\'; document.EditView.lead_account_name.value=\'\';" value="' . $app_strings['LBL_CLEAR_BUTTON_LABEL'] . '" type="button">
     */

    $CallMeetingForm .= '
        </td>   
    </tr>   
    
	<tr>
		<td width="20%" valign="top" scope="row">
			' . $calls_lang['LBL_DATE_TIME'] . '			
			<span class="required">*</span>		
		</td>	
		<td valign="top">
				<table border="0" cellpadding="0" cellspacing="0">
				<tr valign="middle">
				<td nowrap="nowrap">
				<input autocomplete="off" id="date_start_date" value="' . $user_default_date_start . '" size="11" maxlength="10" title="" onblur="combo_date_start.update(); " type="text">
				<img src="themes/default/images/jscalendar.gif" alt="Enter Date" id="date_start_trigger" align="absmiddle" border="0">&nbsp;
				</td>
				<td nowrap="nowrap" valign="top">
					<div id="date_start_time_section" style=\'float:left;\'>

                        ';

    /*
     * 
     *                         <select size="1" id="date_start_hours" onchange="combo_date_start.update();">
      ' . $TIME_START_HOUR_OPTIONS . '
      </select>&nbsp;:
      &nbsp;
      <select size="1" id="date_start_minutes" onchange="combo_date_start.update(); ">
      ' . $TIME_START_MINUTES_OPTIONS . '
      </select>
      &nbsp; . $TIME_MERIDIEM
     * 
     * 
     * 
     */

    /*
     * Replace mm, hh, meridian select boxes with 
     * single input field with hh:mm:{am|pm} format
     * (change #1/2)

      <input type="text" id="date_start_time" value="">;
     */

    /* &nbsp;
      &nbsp; <input type=\'checkbox\' name=\'cal2_whole_day_c\' id=\'cal2_whole_day_c\' onclick="check_whole_day();?"> <br style=\'clear: both;\'>

      </td>
      <td >
      &nbsp;Whole day&nbsp;&nbsp;
      </td> */

    $CallMeetingForm .= '</div>
    
    <td valign="top">
        <input type=\'checkbox\' id=\'cal2_options_c\' name=\'cal2_options_c\'>
    </td>
    <td >
    &nbsp;Private
    </td>
    </tr>
    </table>

    <input id="date_start" name="date_start" value="' . $user_default_date_start . '" type="hidden">
    <script type="text/javascript" src="include/SugarFields/Fields/Datetimecombo/Datetimecombo.js"></script>
    <!-- <script type="text/javascript" src="modules/Calendar2/PageComm.js"></script> -->
    <!-- <script type="text/javascript" src="modules/Calendar2/jsclass_async_cal2.js"></script> -->
    <script type="text/javascript">';

    /*
     * Replace mm, hh, meridian select boxes with 
     * single input field with hh:mm:{am|pm} format
     * (change #2/2)
     */

    /*
      Datetimecombo.prototype.html = function (callback) {
      var time = this.hrs + this.timeseparator + this.mins + this.meridiem;

      var text = ""
      + "<input type=\"text\" "
      + "id=\"date_start_time\" "
      + "value=\"" + time + "\" "
      + "tabindex=\"" + this.tabindex + "\" "
      + "size=\"7\" />"
      ;
      return text;
      }

      Datetimecombo.prototype.jsscript = function(callback) {
      text = "\nfunction update_" + this.fieldname + "(calendar) {";
      text += "\nif(calendar != null) {";
      text += "\ncalendar.onUpdateTime();";
      text += "\ncalendar.onSetTime();";
      text += "\ncalendar.hide();";
      text += "\n}";
      text += "\nd = document.getElementById(\"" + this.fieldname + "_date\").value;";
      text += "\nhm = document.getElementById(\"" + this.fieldname + "_time\").value;";
      text += "\nnewdate = d + \" \" + tm;";
      text += "\ndocument.getElementById(\"" + this.fieldname + "\").value = newdate;";
      text += "\n" + callback;
      text += "\n}";
      return text;
      }

      Datetimecombo.prototype.update = function () {
      id = this.fieldname + "_date";
      d = window.document.getElementById(id).value;
      id = this.fieldname + "_time";
      hm = window.document.getElementById(id).value;
      newdate = d + (" " + hm);
      document.getElementById(this.fieldname).value = newdate;
      if (this.showCheckbox) {
      flag = this.fieldname + "_flag";
      date = this.fieldname + "_date";
      time = this.fieldname + "_time";
      if (document.getElementById(flag).checked) {
      document.getElementById(flag).value = 1;
      document.getElementById(this.fieldname).value = "";
      document.getElementById(date).disabled = true;
      document.getElementById(time).disabled = true;
      } else {
      document.getElementById(flag).value = 0;
      document.getElementById(date).disabled = false;
      document.getElementById(time).disabled = false;
      }
      }
      }
     */

    $CallMeetingForm .= 'var combo_date_start = new Datetimecombo("' . $user_default_date_start . '", "date_start", "' . $TIME_FORMAT . '", 102, \'\', \'\');
    text = combo_date_start.html(\'SugarWidgetScheduler.update_time();\');
    document.getElementById(\'date_start_time_section\').innerHTML = text;
    eval(combo_date_start.jsscript(\'SugarWidgetScheduler.update_time();\'));
    </script>
    <script type="text/javascript">
        function update_date_start_available() {
            YAHOO.util.Event.onAvailable("date_start_date", this.handleOnAvailable, this); 
        }

        update_date_start_available.prototype.handleOnAvailable = function(me) {
            Calendar.setup ({
                onClose : update_date_start,
                inputField : "date_start_date",
                ifFormat : "' . $CALENDAR_FORMAT . '",
                daFormat : "' . $CALENDAR_FORMAT . '",
                button : "date_start_trigger",
                singleClick : true,
                step : 1,
                weekNumbers:false
            });
        	
            //Call update for first time to round hours and minute values
            combo_date_start.update();
        }

        var obj_date_start = new update_date_start_available(); 
    </script>


    </td>

    </tr>';

//<!--     
//    <tr>
//        <td width="20%" valign="top" scope="row">
//            [End Date & Time]
//        </td>   
//        <td valign="top">
//            [Coming soon]
//        </td>   
//    </tr> 
//     
//
//
//     Duration start   -->

    $CallMeetingForm .= '</table>
    <table class="edit view" cellspacing="1" cellpadding="0" border="0" width="100%">
        <tr>
            <td width="20%" valign="top">
                ' . $calls_lang['LBL_DURATION'] . '
                <span class="required">*</span>

            </td>
            <td valign="top">
                <script type="text/javascript">function isValidDuration() { form = document.getElementById(\'EditView\'); if ( form.duration_hours.value + form.duration_minutes.value <= 0 ) { alert(\'' . $calls_lang["NOTICE_DURATION_TIME"] . '\'); return false; } return true; }</script>
                <input name="duration_hours" id="duration_hours" size="2" maxlength="2" type="text" value="0" onkeyup="SugarWidgetScheduler.update_time();"/>
                <select name="duration_minutes" id="duration_minutes" onchange="SugarWidgetScheduler.update_time();">
                    <option value="0">00</option>
                    <option value="15">15</option>
                    <option value="30" selected="">30</option>
                    <option value="45">45</option>
                </select>

                <input type="hidden" name="duration_hours_h" id="duration_hours_h">
                <input type="hidden" name="duration_minutes_h" id="duration_minutes_h">

                <span class="dateFormat">' . $calls_lang["LBL_HOURS_MINUTES"] . '</span>


            </td>
            <td rowspan="6" valign="top">
                <table style="border-collapse: collapse;">
                    <tr>
                        <td class="opp">Opportunity name</td>
                        <td class="opp">Expected close date</td>
                        <td class="opp">Sales stage</td>
                    </tr>
                </table>
                <div id="opportunities" style="display:block; font-size: 10px; height: 94px; overflow : auto;" >
                    <table id="opp_table">
                    </table>
                </div>
                <!--<div width="304" style="border-top-style: solid;border-top-width: 1px; border-top-color:#abc3d7; "></div>-->
            </td>

        </tr>	
        <tr>
            <td width="20%" valign="top" >
                ' . $calls_lang['LBL_REMINDER'] . '
            </td>	
            <td valign="top">	
                <input name="reminder_checked" type="hidden" value="0">
                <input name="reminder_checked" onclick=\'toggleDisplay("should_remind_list");\' type="checkbox" class="checkbox" value="1" >
                <div id="should_remind_list" style="display:none">' . $reminderHTML . '</div>	
            </td>	
        </tr>';

    /*
      <tr>
      <td width="20%" valign="top" scope="row">
      < ?php echo $meetings_lang['LBL_LOCATION'];? >
      </td>
      <td valign="top">
      <input id="location" type="text" title="" value="" maxlength="" size="40" name="location"/>
      </td>
      </tr>
     */
    include_once('modules/Calendar2/functions.php');
    if (isPro()) {
        $CallMeetingForm .= '<tr>';
        if (is551()) {
            $CallMeetingForm .= '	<td width="20%" valign="top" scope="row">';
            $CallMeetingForm .= $calls_lang['LBL_TEAMS'] . ":";
            $CallMeetingForm .= '<span class="required">*</span>';
            $CallMeetingForm .= '</td>';
            $CallMeetingForm .= '<td valign="top">';
            include("modules/Calendar2/TeamsEditView.php");
            $CallMeetingForm .= '</td>';
        } else {
            $CallMeetingForm .= ' <td width="20%" valign="top" scope="row">';
            $CallMeetingForm .= $calls_lang['LBL_TEAM'] . ":";
            $CallMeetingForm .= '<span class="required">*</span>';
            $CallMeetingForm .= '</td>';
            $CallMeetingForm .= '<td valign="top">';
            $CallMeetingForm .= "<input name='cal2_team_name' class='sqsEnabled yui-ac-input'  id='cal2_team_name' size='' value='" . $current_user->default_team_name . "' title='' autocomplete='off' type='text'><div class='yui-ac-container' id='EditView_team_name_results'><div style='display: none;' class='yui-ac-content'><div style='display: none;' class='yui-ac-hd'></div><div class='yui-ac-bd'><ul><li style='display: none;'></li><li style='display: none;'></li><li style='display: none;'></li><li style='display: none;'></li><li style='display: none;'></li><li style='display: none;'></li><li style='display: none;'></li><li style='display: none;'></li><li style='display: none;'></li><li style='display: none;'></li></ul></div><div style='display: none;' class='yui-ac-ft'></div></div></div>";
            $CallMeetingForm .= "<input name='cal2_team_id' id='cal2_team_id' value='" . $current_user->default_team . "' type='hidden'>";
            $CallMeetingForm .= "<input name='btn_team_name' id='btn_team_name'  title='" . $app_strings['LBL_SELECT_BUTTON_TITLE'] . "' accesskey='T' class='button' value='" . $app_strings['LBL_SELECT_BUTTON_LABEL'] . "' onclick='open_popup(\"Teams\", 600, 400, \"\", true, false, {\"call_back_function\":\"set_return\",\"form_name\":\"EditView\",\"field_to_name_array\":{\"id\":\"cal2_team_id\",\"name\":\"cal2_team_name\"}}, \"single\", true);' type='button'>";
            $CallMeetingForm .= "<input name='btn_clr_team_name' id='btn_clr_team_name'  title='" . $app_strings['LBL_CLEAR_BUTTON_TITLE'] . "' accesskey='C' class='button' onclick='this.form.cal2_team_name.value = \"\"; this.form.cal2_team_id.value = \"\";' value='" . $app_strings['LBL_CLEAR_BUTTON_LABEL'] . "' type='button'>";
            $CallMeetingForm .= "</td>";
        }
        $CallMeetingForm .= '</tr>';
    }

    $CallMeetingForm .= '<tr>
        <td width="20%" valign="top" >
            ' . $calls_lang['LBL_ASSIGNED_TO_NAME'] . '
        </td>
        <td valign="top">
            <input name="cal2_assigned_user_name" class="sqsEnabled yui-ac-input" id="cal2_assigned_user_name" size="" value="' . $current_user->user_name . '" title="" autocomplete="off" type="text"><div class="yui-ac-container" id="EditView_assigned_user_name_results"><div style="display: none;" class="yui-ac-content"><div style="display: none;" class="yui-ac-hd"></div><div class="yui-ac-bd"><ul><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li></ul></div><div style="display: none;" class="yui-ac-ft"></div></div></div>
            <input name="cal2_assigned_user_id" id="cal2_assigned_user_id" value="' . $current_user->id . '" type="hidden">
            <input name="btn_assigned_user_name" id="btn_assigned_user_name" title="' . $app_strings['LBL_SELECT_BUTTON_TITLE'] . '" accesskey="T" class="button" value="' . $app_strings['LBL_SELECT_BUTTON_LABEL'] . '" onclick=\'open_popup("Users", 600, 400, "", true, false, {"call_back_function":"set_return", "form_name":"EditView", "field_to_name_array":{"id":"cal2_assigned_user_id", "user_name":"cal2_assigned_user_name"}}, "single", true);
\' type="button">
            <input name="btn_clr_assigned_user_name" id="btn_clr_assigned_user_name" title="' . $app_strings['LBL_CLEAR_BUTTON_TITLE'] . '" accesskey="C" class="button" onclick="this.form.cal2_assigned_user_name.value = \'\'; this.form.cal2_assigned_user_id.value = \'\';" value="' . $app_strings['LBL_CLEAR_BUTTON_LABEL'] . '" type="button">
        </td>	
    </tr>
    <tr>
        <td width="20%" valign="top" >
            ' . $calls_lang['LBL_SEND_BUTTON_LABEL'] . '
        </td>	
        <td valign="top">
            <input type=\'checkbox\' id=\'send_invites\' name=\'send_invites\' value="1">
        </td>
    </tr>	
    <tr>
        <td width="20%" valign="top" >
            ' . $calls_lang['LBL_CATEGORY'] . ':
        </td>	
        <td valign="top">
            <select id=\'cal2_category_c\' name=\'cal2_category_c\'>';

    foreach ($app_list_strings['category_list'] as $k => $v)
        $CallMeetingForm .= '<option label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';

    $CallMeetingForm .= '</select>			
        </td>
    </tr>';

//<!--                    <tr>
//                            <td width="20%" valign="top" >
//    <?php //echo $calls_lang['LBL_PRIVATE'].":"; 
//                            </td>	
//                            <td valign="top">
//                                    <input type='checkbox' id='cal2_options_c' name='cal2_options_c'>			
//                            </td>
//                            
//                            
//                            
//                   </tr>
//
//     End Private-->

    $CallMeetingForm .= '</table>
    <table class="edit view" cellspacing="1" cellpadding="0" border="0" width="100%">

        <tr>
            <td width="20%" valign="top" scope="row">
                <span id="previous_outcome_span" style="display: none;">Previous Outcome:</span>
            </td>
            <td valign="top">
                <textarea  style="display: none;" disabled="disabled" readonly id=\'previous_outcome_c\' name=\'previous_outcome_c\' cols=\'60\' rows=\'2\' tabindex="21"></textarea>
            </td>
        </tr>

        <tr>
            <td width="20%" valign="top" scope="row">
                Pre-Call Plan:
            </td>	
            <td valign="top">
                <textarea id=\'description\' name=\'description\' cols=\'60\' rows=\'4\' tabindex="30"></textarea>
            </td>
        </tr>
        <tr>
            <td width="20%" valign="top" scope="row">
                ' . $calls_lang['LBL_STATUS'] . '
                <span class="required">*</span>
            </td>   
            <td valign="top">       
                <select name="direction" id="direction" title="" style=\'display: none;\'>';

    foreach ($app_list_strings['call_direction_dom'] as $k => $v)
        $CallMeetingForm .= '<option label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';

    $CallMeetingForm .= '</select>
                <select name="status" id="status" title="">';

    foreach ($app_list_strings['call_status_dom'] as $k => $v)
        $CallMeetingForm .= '<option label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';

    $CallMeetingForm .= '</select>
            </td>   
        </tr>
        <tr>
            <td width="20%" valign="top" scope="row">
                ' . $calls_lang['LBL_OUTCOME'] . '
            </td>   
            <td valign="top">       
                <textarea id=\'outcome_c\' name=\'outcome_c\' cols=\'60\' rows=\'4\' tabindex="40"></textarea>
            </td>   
        </tr>
        <tr>
            <td width="20%" valign="top" scope="row">
                <span id="next_date_span" style="display: none;">Date of Next Meeting/Call:</span>
            </td>
            <td valign="top">
                <input type="text" style="display: none;" id=\'next_date\' readonly="readonly" name=\'next_date\' disabled="disabled" size="50" >
            </td>
        </tr>
        <tr>
            <td width="20%" valign="top" scope="row">
                <span id="next_description_span"  style="display: none;">Next Pre-Call Plan:</span>
            </td>
            <td valign="top">
                <textarea id=\'next_description\'  style="display: none;" name=\'next_description\' cols=\'60\' rows=\'4\' tabindex="55"></textarea>
            </td>
        </tr>


        <!--
            <tr>
                <td width="20%" valign="top" scope="row">
                    [Manager Assistance Needed]
                </td>
                <td valign="top">
                    [Yes/No OR symbols signifiying priority level of help needed (low to high)]
                </td>
            </tr>
        -->
    </table>
    <input type="submit" class="button" value="Save" name="Save" id="save_tocalendar_button" onclick="">




<script type="text/javascript">
    function hpad(number, length) {

        if(number == 12 && window.minute == 45) {
            window.hour = 1;
            //number = 1;
            if(window.meridiem == "am") {
               window.meridiem = "pm"; 
            }else{
               window.meridiem = "am"; 
            }
        }else if(window.minute == 45) {
            window.hour = window.hour + 1;
        }

        var str = "" + number;
        

        
        while (str.length < length) {
            str = "0" + str;
        }
        

        return str;
    }
    
    function mpad(number, length) {
        if(number == 45) {
            window.minute = 0;
        }else {
            window.minute = window.minute + 15;

        }

        var str = "" + number;
        

        
        while (str.length < length) {
            str = "0" + str;
        }
        

        return str;
    }
    

    jQuery(document).ready(function() {
        
        
        jQuery("#save_tocalendar_button").click(function() {
            if($("#customers_list :checkbox:checked").length == 0) {
                alert("You need select at least 1 customer from list to procced!");
                return false;
            }
            window.hour = 8;
            window.minute = 0;
            window.progress = 0;
            window.progress_total = $("#customers_list :checkbox:checked");
            window.meridiem = "am";
            $("#EditView #date_start_meridiem").val("am");
            
            $("#customers_list :checkbox:checked").each(function(k, v) {
                //setTimeout("", 2000);
                
                $("#EditView #parent_id").val(v.id);
                $("#EditView #parent_type").val("' . $_REQUEST['module'] . '");';
    if ($_REQUEST['module'] == 'Accounts') {
        $CallMeetingForm .= '
                    $("#EditView #parent_name").val($(v).attr("parent_name"));
                    ';
    } else if ($_REQUEST['module'] == 'Leads') {
        $CallMeetingForm .= '
                    $("#EditView #parent_name").val($(v).attr("parent_name"));
                    var url = "index.php?module=' . $_REQUEST['module'] . '&action=updateLead&lead_id=" + v.id + "&status_desc="+ $("#customers_list input[lead_id="+ v.id +"]").val()+"";
                    $.post(url, function(data){

                    });
                    ';
    }
    $CallMeetingForm .= '
                //alert(hpad(window.hour, 2));
                //alert(mpad(window.minute, 2));
                //alert("call time");
                $("#EditView #date_start_meridiem").val(window.meridiem);
                $("#EditView #date_start_hours").val(hpad(window.hour, 2));
                $("#EditView #date_start_minutes").val(mpad(window.minute, 2));
                combo_date_start.update(); 
                //SugarWidgetScheduler.update_time();
                var formData = $("#EditView").serialize();
                $.ajax({  
                    type: "POST",  
                    url: "index.php?module=Calendar2&action=AjaxSave&sugar_body_only=true&currentmodule=Calendar2&view=month",  
                    data: formData,  
                    success: function() {  
                        var percent_value = Math.round((window.progress / window.progress_total) * 100); 
                        window.progress++;
                        $("#meetings_calls_calendar_quickinput").progressbar("option", "value", percent_value);
                        
                    }  
                });
                

                
               // window.hour = window.hour + 1;
            });
            
            return false;
        });
    
    });




        var disabledModules=[];
        var changeQS = function() {
            new_module = document.getElementById(\'parent_type\').value;

            if(typeof(disabledModules[new_module]) != \'undefined\') {
                sqs_objects[\'parent_name\'][\'disable\'] = true;
                document.getElementById(\'parent_name\').readOnly = true;
            }
            else {
                sqs_objects[\'parent_name\'][\'disable\'] = false;
                document.getElementById(\'parent_name\').readOnly = false;
            }   
            sqs_objects[\'parent_name\'][\'modules\'] = new Array(new_module);

            var type = (new String(new_module)).toLowerCase();
            sqs_objects["parent_name"]["field_list"] = new Array("name", "id");
            sqs_objects["parent_name"]["populate_list"] = new Array("parent_name", "parent_id");
            document.getElementById(\'parent_name_custno_c\').style.display = \'none\';
            document.getElementById(\'lead_account_name\').style.display = \'none\';
            if ( type == "accounts") {
                sqs_objects["parent_name"]["field_list"] = new Array("name", "id", "custno_c");
                sqs_objects["parent_name"]["populate_list"] = new Array("parent_name", "parent_id", "parent_name_custno_c");
                document.getElementById(\'parent_name_custno_c\').style.display = \'inline\';
            }

            if ( type == "leads") {
                sqs_objects["parent_name"]["field_list"] = new Array("name", "id", "account_name");
                sqs_objects["parent_name"]["populate_list"] = new Array("parent_name", "parent_id", "lead_account_name");
                document.getElementById(\'lead_account_name\').style.display = \'inline\';
            }

            enableQS(false);
        }
    </script>
    <style type="text/css">
        .opp {
            text-align: center;
            border-style: solid;
            border-width: 1px;
            border-color: #abc3d7;
            width: 100px;
            font-size: 11px;
        }
        #opp_table {
            border-collapse: collapse;
        }
    </style>';

    return $CallMeetingForm . $quicksearch_js;
}

?>
