<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');


  require_once 'include/QuickSearchDefaults.php';
  require_once('include/utils.php');
  $qsd = new QuickSearchDefaults();

  

 
  $o = $qsd->getQSUser();
  $o['populate_list'] = array("cal2_assigned_user_name_qi", 'cal2_assigned_user_id_qi');
  $o['required_list'][0] = 'cal2_assigned_user_id_qi';
  $sqs_objects['cal2_assigned_user_name_qi'] = $o;

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
function get_sales_reps ($current_user) {
        $str = '<select id="opp_sales_reps_list" size="10" multiple="multiple" style="width: 170px;" onclick="javaScript:get_date_for_table()">';
        $ids = array($current_user->id);
        $o = new fmp_Param_SLSM($current_user->id);
        $o->init();
        $str .= get_select_options_with_id($o->get_sales_reps_array(), '');
        unset($o);
        $str .= '</select>';
        return $str;
}


function html_get_lead_statuses () {
	global $app_list_strings;
	$str = '<select id="leads_status" size="7" multiple="multiple" style="width: 170px;" onclick="javaScript:get_date_for_table()">';
	$str .= get_select_options_with_id($app_list_strings['lead_status_dom'], '');	
	$str .= '</select>';
	return $str;
}

function html_get_lead_source () {
	global $app_list_strings;
	$str = '<select id="leads_source" size="7" multiple="multiple" style="width: 170px;" onclick="javaScript:get_date_for_table()">';
	$str .= get_select_options_with_id($app_list_strings['lead_source_dom'], '');	
	$str .= '</select>';
	return $str;
}



function Call_Meeting_output() {


    require_once("include/utils.php");
    global $current_language;
    global $current_user;
    $calls_lang = return_module_language($current_language, "Calls");
    $meetings_lang = return_module_language($current_language, "Meetings");



    global $app_strings, $app_list_strings, $beanList;
    global $timedate;
    $gmt_default_date_start = $timedate->get_gmt_db_datetime();
    $user_default_date_start = date("m/d/Y");
//customer area slsm dealertype lists
    
    
    $massupdate_class = new QuickInputClass();
    $dealer_list = $massupdate_class->get_dealer_type($app_list_strings['fmp_dealertype_list']);
    $is_user_id = 0;
    $slsm_obj = new fmp_Param_SLSM($current_user->id);

    $slsm_obj->init();

    $is_s = $slsm_obj->is_assigned_slsm();
    if ($is_s) {
//            if(isset($_POST['slsm_num'])) {
        if (isset($_POST['slsm_num']))
            ;
        $arr = Array(0 => null);
//            }
        $r_users = $slsm_obj->compile__available_slsm($arr);
        $str_selection_button = $massupdate_class->build__slsm($r_users, $is_user_id);
    }
    $slsm_tree_list = $slsm_obj->html_for_daily_sales('onclick="javaScript:get_date_for_table()"', '');  // prepeare SLSM list for display
    $reps_list = get_sales_reps($current_user);
    unset($slsm_obj);
    $slsm_area_obj = new fmp_Param_RegLoc($current_user->id);
    $slsm_area_obj->init($current_user->id);
    $area_list = $slsm_area_obj->html_for_daily_sales($current_user->id, 'onclick="javaScript:get_date_for_table()"');

    $status_list = html_get_lead_statuses();
    $source_list = html_get_lead_source();

    unset($slsm_area_obj);
    $call_list = $massupdate_class->scripts_for_display();
    $call_list .= '<div id="meetings_calls_calendar_quickinput">';

    $call_list .= '<div class="yui-skin-sam-fmp-sales">
                                <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="area_list_show">
                                    <span class="first-child-fmp-sales"><button type="button" id="yui-gen0-button" >Area</button>
                                        <div id="area_panel" style="display: none; position: absolute;">
                                            ' . $area_list . '
                                        </div>
                                    </span>
                                </span>
                                <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="slsm_list_show">
                                        <span class="first-child-fmp-sales"><button type="button" id="yui-gen2-button" >Slsm</button>
                                            <div id="slsm_panel" style="display: none; position: absolute; background-color: #FFFFFF; border: 1px solid #94C1E8;">
                                                ' . $slsm_tree_list . '
                                            </div>
                                        </span>
                                </span>

                                <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="dealer_list_show">
                                        <span class="first-child-fmp-sales"><button type="button" id="yui-gen4-button" >Customer Type</button>
                                            <div id="dealer_panel" style="display: none; position: absolute;">
                                                ' . $dealer_list . '
                                            </div>
                                        </span>
                                </span>

                                <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="reps_list_show">
                                        <span class="first-child-fmp-sales"><button type="button" id="yui-gen4-button" >Sales Reps</button>
                                            <div id="reps_panel" style="display: none; position: absolute;">
                                                ' . $reps_list . '
                                            </div>
                                        </span>
                                </span>
                                
                                <span>
                                    Username <input id="call_list_username" type="text" value="" onblur="get_date_for_table();">
                                </span>
                                <span>
                                    City <input id="call_list_city" type="text" value="" onblur="get_date_for_table();">
                                </span>
                                <span>
                                    State <input id="call_list_state" type="text" value="" onblur="get_date_for_table();">
                                </span>
                                <span>
                                    Zip/Postal Code <input id="call_list_postalcode" type="text" value="" onblur="get_date_for_table();">
                                </span>
				<span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="leads_status_show">
					<span class="first-child-fmp-sales"><button type="button" id="yui-gen4-button" >Status</button>
                                            <div id="status_panel" style="display: none; position: absolute;">
				    		' . $status_list . '
                                            </div>
                                        </span>				
				</span>
				<span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="leads_source_show">
					<span class="first-child-fmp-sales"><button type="button" id="yui-gen4-button" >Leads Sources</button>
                                            <div id="source_panel" style="display: none; position: absolute;">
				    		' . $source_list . '
                                            </div>
                                        </span>				
				</span>
                           </div></div>
                           ';


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

        $TIME_MERIDIEM = "<select name='date_start_meridiem_qi' id='date_start_meridiem_qi' tabindex='2'>" . $TIME_MERIDIEM . "</select>";
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

    $CallMeetingForm = '<form id="EditView-QI" class="sendCallMeeting2Calendar">
<input name="return_module_qi" id="return_module_qi" type="hidden" value="Calls">
<input name="cur_module_qi" id="cur_module_qi" type="hidden" value="Calls">
<input name="record_qi" id="form_record_qi" type="hidden" value="">
<!--
<input type="hidden" name="user_invitees_qi">
<input type="hidden" name="resources_assigned_qi">

<input type="hidden" name="cal2_repeat_type_c_qi" id="cal2_repeat_type_c_qi">
<input type="hidden" name="cal2_repeat_interval_c_qi" id="cal2_repeat_interval_c_qi">
<input type="hidden" name="cal2_repeat_end_date_c_qi" id="cal2_repeat_end_date_c_qi">
<input type="hidden" name="cal2_repeat_days_c_qi" id="cal2_repeat_days_c_qi">-->


<input type="hidden" name="edit_all_recurrence_qi" id="edit_all_recurrence_qi">

<input type="hidden" name="cal2_recur_id_c_qi" id="cal2_recur_id_c_qi" value="">

<input type="hidden" name="clicked_record_qi" id="clicked_record_qi" value="">
<input type="hidden" name="clicked_cal2_recur_id_c_qi" id="clicked_cal2_recur_id_c_qi" value="">
<style>
#EditView_parent_name_results { float: left; }
#EditView_assigned_user_name_results { float: left; }
</style>
<table class="edit view" cellspacing="1" cellpadding="0" border="0" width="100%">
	<tr>
		<td   width="20%" valign="top" scope="row">

		</td>
		<td  valign="top">

				<span>
				
		
				<input type="radio" id="radio_meeting_qi" onchange="if(this.checked){this.form.cur_module_qi.value=\'Meetings\'; this.form.return_module_qi.value=\'Meetings\'; document.getElementById(\'direction_qi\').style.display=\'none\'; $(\'select.statuses_direction\').css(\'display\', \'none\'); /*GR_update_focus(\'Meetings\',\'\');*/}"  name="appttype_qi" />
				' . $calls_lang['LNK_NEW_MEETING'] . '
			
				
				<input type="radio" id="radio_call_qi" onchange="if(this.checked){this.form.cur_module_qi.value=\'Calls\'; this.form.return_module_qi.value=\'Calls\'; document.getElementById(\'direction_qi\').style.display = \'inline\';  $(\'select.statuses_direction\').css(\'display\', \'inline\'); /*GR_update_focus(\'Calls\',\'\');*/}" name="appttype_qi" checked="true" />
				
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
        /* $CallMeetingForm .= '
          <input name="parent_id" id="parent_id" value="" type="hidden">
          <input name="parent_name" id="parent_name" value="" type="hidden">
          <input name="parent_type" id="parent_type" value="account" type="hidden">
          '; */
    } else if ($_REQUEST['module'] == 'Leads') {
        /* $CallMeetingForm .= '
          <input name="parent_id" id="parent_id" value="" type="hidden">
          <input name="lead_account_name" id="lead_account_name" value="" type="hidden">
          <input name="parent_type" id="parent_type" value="account" type="hidden">
          '; */
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
          </td> </tr>*/

    $CallMeetingForm .= '

      

      <tr>
      <td width="20%" valign="top" scope="row" >
      Start Date
      <span class="required">*</span>
      </td>
      <td valign="top">
      <table border="0" cellpadding="0" cellspacing="0">
      <tr valign="middle">
      <td nowrap="nowrap">
      <input autocomplete="off" id="date_start_date_qi" value="' . $user_default_date_start . '" size="11" maxlength="10" title="" onblur="" type="text">
      <!-- <img src="themes/default/images/jscalendar.gif" alt="Enter Date" id="date_start_trigger_qi" align="absmiddle" border="0">&nbsp;-->
      <td nowrap="nowrap" valign="top">
      <div id="date_start_time_section_qi" style=\'float:left;\'>
      
     <select size="1" id="date_start_hours_qi" onchange="">
      ' . $TIME_START_HOUR_OPTIONS . '
      </select>&nbsp;:
      &nbsp;
      <select size="1" id="date_start_minutes_qi" onchange="">
      ' . $TIME_START_MINUTES_OPTIONS . '
      </select>
      &nbsp;' . $TIME_MERIDIEM . '
	</div>
      </td>
      </tr>
      </table>
      <input id="date_start_quickinput" name="date_start" value="' . $user_default_date_start . '" type="hidden">
      

      ';

      /*
       * <td nowrap="nowrap" valign="top">
       * <div id="date_start_time_section" style=\'float:left;\'>
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

//    $CallMeetingForm .= '</div>
//    
//    <td valign="top">
//        <input type=\'checkbox\' id=\'cal2_options_c\' name=\'cal2_options_c\'>
//    </td>
//    <td >
//    &nbsp;Private
//    </td>
//    </tr>
//    </table>
//
//    <input id="date_start_quickinput" name="date_start" value="' . $user_default_date_start . '" type="hidden">
//    <script type="text/javascript" src="include/SugarFields/Fields/Datetimecombo/Datetimecombo.js"></script>
//    <!-- <script type="text/javascript" src="modules/Calendar2/PageComm.js"></script> -->
//    <!-- <script type="text/javascript" src="modules/Calendar2/jsclass_async_cal2.js"></script> -->
//    <script type="text/javascript">';
//
//    /*
//     * Replace mm, hh, meridian select boxes with 
//     * single input field with hh:mm:{am|pm} format
//     * (change #2/2)
//     */
//
//    /*
//      Datetimecombo.prototype.html = function (callback) {
//      var time = this.hrs + this.timeseparator + this.mins + this.meridiem;
//
//      var text = ""
//      + "<input type=\"text\" "
//      + "id=\"date_start_time\" "
//      + "value=\"" + time + "\" "
//      + "tabindex=\"" + this.tabindex + "\" "
//      + "size=\"7\" />"
//      ;
//      return text;
//      }
//
//      Datetimecombo.prototype.jsscript = function(callback) {
//      text = "\nfunction update_" + this.fieldname + "(calendar) {";
//      text += "\nif(calendar != null) {";
//      text += "\ncalendar.onUpdateTime();";
//      text += "\ncalendar.onSetTime();";
//      text += "\ncalendar.hide();";
//      text += "\n}";
//      text += "\nd = document.getElementById(\"" + this.fieldname + "_date\").value;";
//      text += "\nhm = document.getElementById(\"" + this.fieldname + "_time\").value;";
//      text += "\nnewdate = d + \" \" + tm;";
//      text += "\ndocument.getElementById(\"" + this.fieldname + "\").value = newdate;";
//      text += "\n" + callback;
//      text += "\n}";
//      return text;
//      }
//
//      Datetimecombo.prototype.update = function () {
//      id = this.fieldname + "_date";
//      d = window.document.getElementById(id).value;
//      id = this.fieldname + "_time";
//      hm = window.document.getElementById(id).value;
//      newdate = d + (" " + hm);
//      document.getElementById(this.fieldname).value = newdate;
//      if (this.showCheckbox) {
//      flag = this.fieldname + "_flag";
//      date = this.fieldname + "_date";
//      time = this.fieldname + "_time";
//      if (document.getElementById(flag).checked) {
//      document.getElementById(flag).value = 1;
//      document.getElementById(this.fieldname).value = "";
//      document.getElementById(date).disabled = true;
//      document.getElementById(time).disabled = true;
//      } else {
//      document.getElementById(flag).value = 0;
//      document.getElementById(date).disabled = false;
//      document.getElementById(time).disabled = false;
//      }
//      }
//      }
//     */
//
//    $CallMeetingForm .= 'var combo_date_start = new Datetimecombo("' . $user_default_date_start . '", "date_start", "' . $TIME_FORMAT . '", 102, \'\', \'\');
//    text = combo_date_start.html(\'SugarWidgetScheduler.update_time();\');
//    document.getElementById(\'date_start_time_section\').innerHTML = text;
//    eval(combo_date_start.jsscript(\'SugarWidgetScheduler.update_time();\'));
//    </script>
//
//
//    </td>
//
//    </tr>';
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
                <script type="text/javascript">function isValidDuration() { form = document.getElementById(\'EditView-QI\'); if ( form.duration_hours_qi.value + form.duration_minutes_qi.value <= 0 ) { alert(\'' . $calls_lang["NOTICE_DURATION_TIME"] . '\'); return false; } return true; }</script>
                <input name="duration_hours_qi" id="duration_hours_qi" size="2" maxlength="2" type="text" value="0" onkeyup="SugarWidgetScheduler.update_time();"/>
                <select name="duration_minutes_qi" id="duration_minutes_qi" onchange="SugarWidgetScheduler.update_time();">
                    <option value="0">00</option>
                    <option value="15"  selected="">15</option>
                    <option value="30">30</option>
                    <option value="45">45</option>
                </select>

                <input type="hidden" name="duration_hours_h_qi" id="duration_hours_h_qi">
                <input type="hidden" name="duration_minutes_h_qi" id="duration_minutes_h_qi">

                <span class="dateFormat">' . $calls_lang["LBL_HOURS_MINUTES"] . '</span>


            </td>
            <td rowspan="6" valign="top">
                <!--<table style="border-collapse: collapse;">
                    <tr>
                        <td class="opp">Opportunity name</td>
                        <td class="opp">Expected close date</td>
                        <td class="opp">Sales stage</td>
                    </tr>
                </table>-->

                <!--<div width="304" style="border-top-style: solid;border-top-width: 1px; border-top-color:#abc3d7; "></div>-->
            </td>

        </tr>	
        <tr>
            <!--<td width="20%" valign="top" >
                ' . $calls_lang['LBL_REMINDER'] . '
            </td>	
            <td valign="top">	
                <input name="reminder_checked_qi" type="hidden" value="0">
                <input name="reminder_checked_qi" onclick=\'toggleDisplay("should_remind_list");\' type="checkbox" class="checkbox" value="1" >
                <div id="should_remind_list_qi" style="display:none">' . $reminderHTML . '</div>	
            </td>-->	
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
            $CallMeetingForm .= "<input name='cal2_team_name_qi' class='sqsEnabled yui-ac-input'  id='cal2_team_name_qi' size='' value='" . $current_user->default_team_name . "' title='' autocomplete='off' type='text'><div class='yui-ac-container' id='EditView_team_name_results'><div style='display: none;' class='yui-ac-content'><div style='display: none;' class='yui-ac-hd'></div><div class='yui-ac-bd'><ul><li style='display: none;'></li><li style='display: none;'></li><li style='display: none;'></li><li style='display: none;'></li><li style='display: none;'></li><li style='display: none;'></li><li style='display: none;'></li><li style='display: none;'></li><li style='display: none;'></li><li style='display: none;'></li></ul></div><div style='display: none;' class='yui-ac-ft'></div></div></div>";
            $CallMeetingForm .= "<input name='cal2_team_id_qi' id='cal2_team_id_qi' value='" . $current_user->default_team . "' type='hidden'>";
            $CallMeetingForm .= "<input name='btn_team_name_qi' id='btn_team_name_qi'  title='" . $app_strings['LBL_SELECT_BUTTON_TITLE'] . "' accesskey='T' class='button' value='" . $app_strings['LBL_SELECT_BUTTON_LABEL'] . "' onclick='open_popup(\"Teams\", 600, 400, \"\", true, false, {\"call_back_function\":\"set_return\",\"form_name\":\"EditView-QI\",\"field_to_name_array\":{\"id\":\"cal2_team_id_qi\",\"name\":\"cal2_team_name_qi\"}}, \"single\", true);' type='button'>";
            $CallMeetingForm .= "<input name='btn_clr_team_name_qi' id='btn_clr_team_name_qi'  title='" . $app_strings['LBL_CLEAR_BUTTON_TITLE'] . "' accesskey='C' class='button' onclick='this.form.cal2_team_name_qi.value = \"\"; this.form.cal2_team_id_qi.value = \"\";' value='" . $app_strings['LBL_CLEAR_BUTTON_LABEL'] . "' type='button'>";
            $CallMeetingForm .= "</td>";
        }
        $CallMeetingForm .= '</tr>';
    }
    $CallMeetingForm .= '<tr>
      <td width="20%" valign="top" >
      ' . $calls_lang['LBL_ASSIGNED_TO_NAME'] . '
      </td>
      <td valign="top">
      <input name="cal2_assigned_user_name_qi" class="sqsEnabled yui-ac-input" id="cal2_assigned_user_name_qi" size="" value="' . $current_user->user_name . '" title="" autocomplete="off" type="text"><div class="yui-ac-container" id="EditView_assigned_user_name_results_qi"><div style="display: none;" class="yui-ac-content"><div style="display: none;" class="yui-ac-hd"></div><div class="yui-ac-bd"><ul><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li></ul></div><div style="display: none;" class="yui-ac-ft"></div></div></div>
      <input name="cal2_assigned_user_id_qi" id="cal2_assigned_user_id_qi" value="' . $current_user->id . '" type="hidden">
      <input name="btn_assigned_user_name_qi" id="btn_assigned_user_name_qi" title="' . $app_strings['LBL_SELECT_BUTTON_TITLE'] . '" accesskey="T" class="button" value="' . $app_strings['LBL_SELECT_BUTTON_LABEL'] . '" onclick=\'open_popup("Users", 600, 400, "", true, false, {"call_back_function":"set_return", "form_name":"EditView", "field_to_name_array":{"id":"cal2_assigned_user_id_qi", "user_name":"cal2_assigned_user_name_qi"}}, "single", true);
      \' type="button">
      <input name="btn_clr_assigned_user_name_qi" id="btn_clr_assigned_user_name_qi" title="' . $app_strings['LBL_CLEAR_BUTTON_TITLE'] . '" accesskey="C" class="button" onclick="this.form.cal2_assigned_user_name_qi.value = \'\'; this.form.cal2_assigned_user_id_qi.value = \'\';" value="' . $app_strings['LBL_CLEAR_BUTTON_LABEL'] . '" type="button">
      </td>
      </tr>';


/*    <tr>
        <td width="20%" valign="top" >
            ' . $calls_lang['LBL_SEND_BUTTON_LABEL'] . '
        </td>	
        <td valign="top">
            <input type=\'checkbox\' id=\'send_invites_qi\' name=\'send_invites_qi\' value="1">
        </td>
    </tr>*/
    $CallMeetingForm .= '
	
    <tr>
        <td width="20%" valign="top" >
            ' . $calls_lang['LBL_CATEGORY'] . ':
        </td>	
        <td valign="top">
            <select id=\'cal2_category_c_qi\' name=\'cal2_category_c_qi\'>';

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
                <textarea  style="display: none;" disabled="disabled" readonly id=\'previous_outcome_c_qi\' name=\'previous_outcome_c_qi\' cols=\'60\' rows=\'2\' tabindex="21"></textarea>
            </td>
        </tr>';
    /*  <tr>
      <td width="20%" valign="top" scope="row">
      Pre-Call Plan:
      </td>
      <td valign="top">
      <textarea id=\'description\' name=\'description\' cols=\'60\' rows=\'4\' tabindex="30"></textarea>
      </td>
      </tr> */


    /*$CallMeetingForm .= '<tr class="selecting-input-statuses-row">
            <td width="20%" valign="top" scope="row">
                ' . $calls_lang['LBL_STATUS'] . '
                <span class="required">*</span>
            </td>   
            <td valign="top" class="selecting-input-statuses">       
                <select name="direction_qi" id="direction_qi" title="">';

    foreach ($app_list_strings['call_direction_dom'] as $k => $v)
        $CallMeetingForm .= '<option label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';

    $CallMeetingForm .= '</select>
                <select name="status_qi" id="status_qi" title="">';

    foreach ($app_list_strings['call_status_dom'] as $k => $v)
        $CallMeetingForm .= '<option label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';

    $CallMeetingForm .= '</select>
            </td>   
        </tr>';*/

    /*        <tr>
      <td width="20%" valign="top" scope="row">
      ' . $calls_lang['LBL_OUTCOME'] . '
      </td>
      <td valign="top">
      <textarea id=\'outcome_c\' name=\'outcome_c\' cols=\'60\' rows=\'4\' tabindex="40"></textarea>
      </td>
      </tr>
     */
    /*
     *  <tr>
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
     */

/*

';
    //if ($_REQUEST['module'] == 'Accounts') {

              ';
    //} else if ($_REQUEST['module'] == 'Leads') {
//        $CallMeetingForm .= '
//                    $("#EditView #parent_name").val($(v).attr("parent_name"));
//                    var url = "index.php?module=' . $_REQUEST['module'] . '&action=updateLead&lead_id=" + v.id + "&status_desc="+ $("#customers_list input[lead_id="+ v.id +"]").val()+"";
//                    $.post(url, function(data){
//
//                    });
//                    ';
    //}
    $CallMeetingForm .= '*/
       // $CallMeetingForm .= '*/


/*    $CallMeetingFormSubmit = '<input type="submit" class="button" value="Save" name="Save" id="save_tocalendar_button" onclick="CustomerListcreateRecords(); return false;">';*/
    $CallMeetingFormSubmit = '<input type="submit" class="button" value="Save" name="Save" id="save_tocalendar_button" >';

    $CallMeetingForm .= '
    </table>
    

    <script type="text/javascript">

    function hpad(number, length) {
	console.log("hpad() hour: ", window.hour);
	console.log("hpad() min: ", window.minute);
	console.log("hpad() meridiem: ", window.meridiem);
        if (number == 11 && window.minute == 45) {
	    if(window.meridiem == "am" || window.meridiem == undefined) {
               window.meridiem = "pm"; 
            } else {
               window.meridiem = "am"; 
            }
	    window.hour = window.hour + 1;
	
	} else if(number == 12 && window.minute == 45) {
            window.hour = 1;

        } else if(window.minute == 45) {
            window.hour = window.hour + 1;
        }

        var str = "" + number;
        

        
        while (str.length < length) {
            str = "0" + str;
        }
        

        return str;
    }
    
    function mpad(number, length) {
	console.log("mpad() hour: ", window.hour);
	console.log("mpad() min: ", window.minute);
	console.log("mpad() meridiem: ", window.meridiem);

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
    


        
        
        function CustomerListcreateRecords (type) {
		//console.log("type_value " + type);
            if(selected_customers.length == 0) {
                alert("You need select at least 1 customer from list to procced!");
                return false;
            }
            //window.minute = 0;
	    //window.hour = 8;
	    //window.meridiem = "am";
	    window.hour = Number($("#date_start_hours_qi").val());
	    window.minute = Number($("#date_start_minutes_qi").val());
	    window.meridiem = $("#date_start_meridiem_qi").val();
	    $("#EditView #date_start_meridiem").val("am");
            
            window.progress = 0;
            window.progress_total = $("#customers_list :checkbox:checked");
            
            

             for(var i = 0; i < selected_customers.length;i++ ){
				 if(typeof selected_customers[i] != "undefined"){
                var v = selected_customers[i];
                $("#EditView #parent_id").val(v.id);

		if(type == "current-customer-call-list") {
                	$("#EditView #parent_type").val("Accounts");
			//$("#EditView #parent_type").trigger("change");
			$("#EditView #parent_name").val($(v).attr("parent_name"));
			$("#EditView #name").val($(v).attr("cust_no") + " " + $(v).attr("parent_name"));
		} else if (type == "leads-call-list") {
			$("#EditView #parent_type").val("Leads");
			//$("#EditView #parent_type").trigger("change");
			$("#EditView #lead_account_name").val($(v).attr("lead_account_name"));
			$("#EditView #parent_name").val($(v).attr("parent_name"));
			$("#EditView #name").val($(v).attr("lead_account_name") + " (" + $(v).attr("parent_name") + ")");
			var url = "index.php?module=Leads&action=updateLead&lead_id=" + v.id + "&status_desc="+ $("#customers_list .status_description[lead_id="+ v.id +"]").val()+"";
                    $.post(url, function(data){


                    });
		}               
		
  
                
                jQuery("form#EditView-QI :input").not(".button").each(function() {
		 var tag_name = "";
		    var suffis_pos = "";
		    var original_id = "";
		    tag_name = jQuery(this).attr("name");
		    suffis_pos = tag_name.indexOf("_qi");
		    original_id = tag_name.substr(0, suffis_pos);
		
		    jQuery("form#EditView :input[name=\'"+original_id+"\']").val(jQuery(this).val());
                });

                $("#EditView #description").val($("#customers_list .pc-plan[account_id_pcplan="+v.id+"]").val());
                $("#EditView #outcome_c").val($("#customers_list .outcome[account_id_outcome="+v.id+"]").val());
		$("#EditView #direction").val($("#customers_list .statuses_direction[account_id_statuses_direction="+v.id+"]").val());
		$("#EditView #status").val($("#customers_list .statuses_status[account_id_statuses_status="+v.id+"]").val());
		$("#EditView #date_start_date").val($("#date_start_date_qi").val());
                $("#EditView #date_start_meridiem").val(window.meridiem);
                $("#EditView #date_start_hours").val(hpad(window.hour, 2));
                $("#EditView #date_start_minutes").val(mpad(window.minute, 2));
		

                combo_date_start.update(); 
                SugarWidgetScheduler.update_time();
                var formData = $("#EditView").serialize();
		window.previmported = new Array();
                $.ajax({  
                    type: "POST",  
                    url: "index.php?module=Calendar2&action=AjaxSave&sugar_body_only=true&currentmodule=Calendar2&view=month",  
                    data: formData,
		    dataType: "json",  
                    success: function(res) {
									
			window.previmported[window.previmported.length] = res;
			sessionStorage.setItem("previous_records", JSON.stringify(window.previmported));
			AddRecords(res);
                        
                    }  
                });
                

          
			
				}
               // window.hour = window.hour + 1;
            }

            $("#record_dialog_quick_input").dialog("close");  
            return false;
        }




        function CustomerListupdateRecords (type) {

            if(selected_customers.length == 0) {
                alert("You need select at least 1 customer from list to procced!");
                return false;
            }
            window.hour = 8;
            window.minute = 0;
            window.progress = 0;
            window.progress_total = $(selected_customers);
            window.meridiem = "am";
            $("#EditView #date_start_meridiem").val("am");

             for(var i = 0; i < selected_customers.length; i++ ){
				 if(typeof selected_customers[i] != "undefined"){
					 var v = selected_customers[i];
                var current_record = new Object();
                $("#EditView #parent_id").val(v.id);
                $("#EditView #parent_type").val("Accounts");
                $("#EditView #parent_name").val($(v).attr("parent_name"));

		if(type == "current-customer-call-list" || type == "accounts") {
                	$("#EditView #parent_type").val("Accounts");
			//$("#EditView #parent_type").trigger("change");
			$("#EditView #parent_name").val($(v).attr("parent_name"));
			$("#EditView #name").val($(v).attr("cust_no") + " " + $(v).attr("parent_name"));
		} else if (type == "leads-call-list"  || type == "leads") {
			$("#EditView #parent_type").val("Leads");
			//$("#EditView #parent_type").trigger("change");
			$("#EditView #lead_account_name").val($(v).attr("lead_account_name"));
			$("#EditView #parent_name").val($(v).attr("parent_name"));
			$("#EditView #name").val($(v).attr("lead_account_name") + " (" + $(v).attr("parent_name") + ")");
			var url = "index.php?module=Leads&action=updateLead&lead_id=" + v.id + "&status_desc="+ $("#customers_list .status_description[lead_id="+ v.id +"]").val()+"";
                    $.post(url, function(data){


                    });
		} 

		
		for(var i= 0; i<window.previmported.length; i++) {
			console.log("record " + window.previmported[i].record);
			console.log("record from table " + $("#customers_list input#" + v.id).attr("record_id"));
			if(window.previmported[i].record == $("#customers_list input#" + v.id).attr("record_id")) {
				
				current_record = window.previmported[i];
				break;
			}
		}  
		//console.log(v.id);
		//console.log(window.previmported);
		//console.log($("#customers_list input#" + v.id));
		console.log(current_record);
                jQuery("form#EditView-QI :input").not(".button").each(function() {
		 var tag_name = "";
		    var suffis_pos = "";
		    var original_id = "";
		    tag_name = jQuery(this).attr("name");
		    suffis_pos = tag_name.indexOf("_qi");
		    original_id = tag_name.substr(0, suffis_pos);
		
		    jQuery("form#EditView :input[name=\'"+original_id+"\']").val(jQuery(this).val());
                });
		
                $("#EditView #description").val($("#customers_list .pc-plan[account_id_pcplan="+v.id+"]").val());
                $("#EditView #outcome_c").val($("#customers_list .outcome[account_id_outcome="+v.id+"]").val());
		$("#EditView #direction").val($("#customers_list .statuses_direction[account_id_statuses_direction="+v.id+"]").val());
		$("#EditView #status").val($("#customers_list .statuses_status[account_id_statuses_status="+v.id+"]").val());
		$("#EditView #name").val($(v).attr("cust_no") + " " + $(v).attr("parent_name"));
                //$("#EditView #date_start_meridiem").val(window.meridiem);
                //$("#EditView #date_start_hours").val(hpad(window.hour, 2));
                //$("#EditView #date_start_minutes").val(mpad(window.minute, 2));

		$("#EditView #clicked_record").val($("#customers_list input#" + v.id).attr("record_id"));
		$("#EditView #form_record").val($("#customers_list input#" + v.id).attr("record_id"));
		
		
		
		//jQuery("#EditView #date_start_date").val(current_record.date_start);
		jQuery("#EditView #date_start").val(current_record.date_start);

                //combo_date_start.update(); 
                //SugarWidgetScheduler.update_time();
                var formData = $("#EditView").serialize();
		
                $.ajax({  
                    type: "POST",  
                    url: "index.php?module=Calendar2&action=AjaxSave&sugar_body_only=true&currentmodule=Calendar2&view=month",  
                    data: formData,
		    dataType: "json",  
                    success: function(res) {
									
			//window.previmported[window.previmported.length] = res;
			AddRecords(res);
                        
                    }  
                });
                

             }   
               // window.hour = window.hour + 1;
            }
            $("#record_dialog_quick_input").dialog("close");  
            return false;
        }
    




//        var disabledModules=[];
//        var changeQS = function() {
//            new_module = document.getElementById(\'parent_type\').value;
//
//            if(typeof(disabledModules[new_module]) != \'undefined\') {
//                sqs_objects[\'parent_name\'][\'disable\'] = true;
//                document.getElementById(\'parent_name\').readOnly = true;
//            }
//            else {
//                sqs_objects[\'parent_name\'][\'disable\'] = false;
//                document.getElementById(\'parent_name\').readOnly = false;
//            }   
//            sqs_objects[\'parent_name\'][\'modules\'] = new Array(new_module);
//
//            var type = (new String(new_module)).toLowerCase();
//            sqs_objects["parent_name"]["field_list"] = new Array("name", "id");
//            sqs_objects["parent_name"]["populate_list"] = new Array("parent_name", "parent_id");
//            document.getElementById(\'parent_name_custno_c\').style.display = \'none\';
//            document.getElementById(\'lead_account_name\').style.display = \'none\';
//            if ( type == "accounts") {
//                sqs_objects["parent_name"]["field_list"] = new Array("name", "id", "custno_c");
//                sqs_objects["parent_name"]["populate_list"] = new Array("parent_name", "parent_id", "parent_name_custno_c");
//                document.getElementById(\'parent_name_custno_c\').style.display = \'inline\';
//            }
//
//            if ( type == "leads") {
//                sqs_objects["parent_name"]["field_list"] = new Array("name", "id", "account_name");
//                sqs_objects["parent_name"]["populate_list"] = new Array("parent_name", "parent_id", "lead_account_name");
//                document.getElementById(\'lead_account_name\').style.display = \'inline\';
//            }
//
//            enableQS(false);
//        }
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

    $table = '<div id="customers-list-to-calendar">
		<table id="customers_list" style="width: 100%">
                        <thead>
                            <th style="width: 30px;">Include</th>
                            <th>CustNo</th>
                            <th style="width: 300px;">CustName</th>
                            <th style="width: 30px;">Address</th>
                            <th style="width: 30px;">City</th>
                            <th style="width: 30px;">State</th>
                            <th style="width: 30px;">PostalCode</th>
                            <th>Contact</th>
                            <th>Phone</th>
                            <th  style="width: 30px;">MTD Sales</th>
                            <th  style="width: 30px;">MTD Proj vs. Budget</th>
                            <th  style="width: 30px;">YTD Proj vs. Budget</th>
			    <th class="status-table-header" style="width: 30px;">Status</th>
                            <th class="pre-call-plan-table-header" style="width: 300px;">Pre-Call Plan</th>
                            <th class="outcome-table-header" style="width: 300px;">Outcome</th>
                        </thead>
                        <tbody>
			    <tr>
                        	<td colspan="11" class="dataTables_empty">Loading data from server</td>
                	    </tr>
		  	</tbody>
		</table>
	      </div>';

    return $call_list . $CallMeetingForm  . '</form>' . $table . $CallMeetingFormSubmit . $quicksearch_js;
}
?>
